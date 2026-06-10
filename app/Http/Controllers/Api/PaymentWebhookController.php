<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Tiếp nhận Webhook từ cổng thanh toán đối soát tự động (PayOS/SePay/Casso)
     */
    public function handleWebhook(Request $request)
    {
        Log::info('Payment Webhook received payload:', $request->all());

        // 1. Nhận dạng payload và nhà cung cấp
        $provider = $this->detectProvider($request);
        if (!$provider) {
            return response()->json(['success' => false, 'message' => 'Unsupported provider'], 400);
        }

        // 2. Trích xuất thông tin thanh toán (mã tham chiếu và số tiền)
        $paymentData = $this->extractPaymentData($provider, $request);
        if (!$paymentData || empty($paymentData['reference'])) {
            return response()->json(['success' => false, 'message' => 'Cannot extract reference code'], 400);
        }

        $reference = $paymentData['reference'];
        $amount = (float) $paymentData['amount'];

        // 3. Tìm Bill bằng mã chuyển khoản (Ví dụ nội dung là "SRM12" -> ID là 12)
        if (!preg_match('/SRM(\d+)/i', $reference, $matches)) {
            Log::info("Webhook ignored description: {$reference} (no SRM pattern)");
            return response()->json(['success' => true, 'message' => 'Webhook received but ignored (no SRM pattern)']);
        }

        $billId = $matches[1];
        $bill = Bill::with(['tenant', 'room'])->find($billId);

        if (!$bill) {
            Log::warning("Bill not found for ID: {$billId}");
            return response()->json(['success' => false, 'message' => 'Bill not found'], 404);
        }

        // Nếu hóa đơn đã được thanh toán rồi, báo thành công luôn để tránh lặp webhook
        if ($bill->status === 'paid') {
            return response()->json(['success' => true, 'message' => 'Bill already marked as paid']);
        }

        // 4. Xác minh chữ ký bảo mật dựa trên config của Tenant chủ trọ đó
        $tenant = $bill->tenant;
        $config = $tenant->payment_gateway_config;
        
        if ($config && !$this->verifySignature($provider, $request, $config)) {
            Log::warning("Signature verification failed for Tenant ID {$tenant->id} on Bill {$billId}");
            return response()->json(['success' => false, 'message' => 'Invalid signature verification'], 403);
        }

        // 5. Cập nhật hóa đơn và ghi nhận giao dịch vào Sổ Quỹ Thu Chi trong DB Transaction
        try {
            DB::transaction(function () use ($bill, $amount) {
                // Đổi trạng thái hóa đơn
                $bill->update([
                    'status' => 'paid',
                    'payment_date' => now(),
                ]);

                // Tự động ghi chép một giao dịch THU vào Sổ quỹ thu chi của chủ trọ đó
                Transaction::create([
                    'tenant_id' => $bill->tenant_id,
                    'type' => 'income',
                    'amount' => $amount,
                    'category' => 'rental',
                    'description' => "Thanh toán tự động hóa đơn phòng {$bill->room->room_number} tháng {$bill->billing_month} (Mã SRM{$bill->id})",
                    'transaction_date' => now(),
                ]);

                Log::info("Bill {$bill->id} successfully reconciled and marked as paid via webhook.");
            });

            // Ở đây có thể tích hợp gửi tin nhắn Zalo/SMS biên nhận qua SMS service...
            return response()->json(['success' => true, 'message' => 'Reconciliation successful']);

        } catch (\Exception $e) {
            Log::error("Failed to process payment callback for bill {$billId}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Database error during transaction'], 500);
        }
    }

    /**
     * Nhận biết nhà cung cấp webhook dựa trên header hoặc cấu trúc payload
     */
    private function detectProvider(Request $request): ?string
    {
        if ($request->has('code') && $request->has('desc') && $request->has('data')) {
            return 'payos';
        }
        if ($request->has('gateway') && $request->has('transaction')) {
            return 'sepay';
        }
        if ($request->has('casso')) {
            return 'casso';
        }
        // Fallback nhận dạng dạng đơn giản để dễ test
        if ($request->has('gateway')) {
            return 'sepay';
        }
        return 'payos'; // default
    }

    /**
     * Trích xuất thông tin mã chuyển khoản và số tiền từ payload
     */
    private function extractPaymentData(string $provider, Request $request): ?array
    {
        if ($provider === 'payos') {
            $data = $request->input('data', []);
            return [
                'reference' => $data['description'] ?? '',
                'amount' => $data['amount'] ?? 0,
            ];
        }
        if ($provider === 'sepay') {
            return [
                'reference' => $request->input('content') ?? $request->input('transaction.content') ?? '',
                'amount' => $request->input('transferAmount') ?? $request->input('transaction.transferAmount') ?? 0,
            ];
        }
        return null;
    }

    /**
     * Xác thực tính toàn vẹn chữ ký Webhook từ nhà cung cấp
     */
    private function verifySignature(string $provider, Request $request, array $config): bool
    {
        // Giả sử có token xác thực hoặc webhook secret key trong config của tenant
        if (empty($config['checksum_key']) && empty($config['webhook_token'])) {
            // Nếu chủ trọ chưa cấu hình key bảo mật, tạm thời bỏ qua bước check signature để hoạt động basic
            return true; 
        }

        if ($provider === 'payos') {
            // Logic verify signature của PayOS sử dụng checksum_key
            $data = $request->input('data');
            $signature = $request->input('signature');
            
            if (!$data || !$signature) return false;
            
            // Sắp xếp key dữ liệu tăng dần theo bảng chữ cái để tạo chuỗi ký tự
            ksort($data);
            $queryString = http_build_query($data);
            $calculatedSignature = hash_hmac('sha256', $queryString, $config['checksum_key'] ?? '');
            
            return hash_equals($calculatedSignature, $signature);
        }

        if ($provider === 'sepay') {
            // Đối với SePay thường sử dụng Token trong Authorization Header
            $authHeader = $request->header('Authorization');
            $expectedToken = 'Apikey ' . ($config['webhook_token'] ?? '');
            return $authHeader === $expectedToken;
        }

        return true;
    }
}
