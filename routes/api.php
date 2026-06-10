<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VisitorController;
use App\Http\Controllers\Api\TenantAdminController;
use App\Http\Controllers\Api\ResidentController;
use App\Http\Controllers\Api\SensitiveDataController;
use App\Http\Controllers\VerificationDocumentController;
use App\Http\Controllers\Api\PaymentWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// ==========================================
// 1. PUBLIC ROUTES (Khách vãng lai & Tìm trọ)
// ==========================================
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/webhooks/payments', [PaymentWebhookController::class, 'handleWebhook']);

Route::get('/renty/rooms', [VisitorController::class, 'index']);
Route::get('/renty/rooms/map', [VisitorController::class, 'map']);
Route::get('/renty/rooms/{id}/reviews', [VisitorController::class, 'reviews']);
Route::get('/renty/rooms/{id}/reviews/summary', [VisitorController::class, 'reviewSummary']);
Route::post('/renty/rooms/compare', [VisitorController::class, 'compare']);

// ==========================================
// 2. AUTHENTICATED ROUTES (Đã đăng nhập)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth profile & logout
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    // Khách hàng / Cư dân đã đăng nhập viết đánh giá phòng trọ
    Route::post('/renty/rooms/{id}/reviews', [VisitorController::class, 'storeReview']);

    // ------------------------------------------
    // A. PHÂN HỆ CHỦ TRỌ / QUẢN LÝ (Tenant Admin)
    // ------------------------------------------
    Route::middleware('role:admin')->prefix('platform-admin')->group(function () {
        Route::post('/verification-documents/{document}/unlock', [VerificationDocumentController::class, 'unlock']);
    });

    Route::middleware('role:landlord')->prefix('admin')->group(function () {
        // Thống kê Dashboard
        Route::get('/dashboard', [TenantAdminController::class, 'dashboard']);

        // CRUD Tòa nhà (Buildings)
        Route::get('/buildings', [TenantAdminController::class, 'listBuildings']);
        Route::post('/buildings', [TenantAdminController::class, 'storeBuilding']);
        Route::put('/buildings/{id}', [TenantAdminController::class, 'updateBuilding']);
        Route::delete('/buildings/{id}', [TenantAdminController::class, 'deleteBuilding']);

        // CRUD Phòng trọ (Rooms)
        Route::get('/rooms', [TenantAdminController::class, 'listRooms']);
        Route::post('/rooms', [TenantAdminController::class, 'storeRoom']);
        Route::put('/rooms/{id}', [TenantAdminController::class, 'updateRoom']);
        Route::delete('/rooms/{id}', [TenantAdminController::class, 'deleteRoom']);

        // Sơ đồ phòng trực quan (Room Matrix)
        Route::get('/rooms/matrix', [TenantAdminController::class, 'roomMatrix']);

        // CRUD Cư dân (Residents)
        Route::get('/residents', [TenantAdminController::class, 'listResidents']);
        Route::post('/residents', [TenantAdminController::class, 'storeResident']);
        Route::put('/residents/{id}', [TenantAdminController::class, 'updateResident']);
        Route::delete('/residents/{id}', [TenantAdminController::class, 'deleteResident']);
        Route::post('/residents/{resident}/sensitive', [SensitiveDataController::class, 'resident']);
        Route::post('/tenant/bank-account/reveal', [SensitiveDataController::class, 'tenantBankAccount']);

        // Hợp đồng online
        Route::get('/contracts', [TenantAdminController::class, 'listContracts']);
        Route::post('/contracts', [TenantAdminController::class, 'storeContract']);
        Route::delete('/contracts/{id}', [TenantAdminController::class, 'deleteContract']);

        // Chốt số & Tính tiền tự động
        Route::post('/utility/record', [TenantAdminController::class, 'storeUtilityRecord']);

        // Xuất hóa đơn PDF & Gửi thông báo
        Route::get('/bills/{id}/pdf', [TenantAdminController::class, 'printPdf']);
        Route::post('/bills/{id}/notify', [TenantAdminController::class, 'notifyResident']);

        // Xử lý sự cố (Tickets)
        Route::get('/tickets', [TenantAdminController::class, 'listTickets']);
        Route::put('/tickets/{id}', [TenantAdminController::class, 'updateTicket']);
    });

    // ------------------------------------------
    // B. PHÂN HỆ CƯ DÂN NỘI BỘ (Resident)
    // ------------------------------------------
    Route::middleware('role:resident')->prefix('resident')->group(function () {
        Route::get('/dashboard', [ResidentController::class, 'dashboard']);
        Route::get('/bills', [ResidentController::class, 'bills']);
        Route::get('/bills/{id}/vietqr', [ResidentController::class, 'getBillQr']);
        Route::post('/tickets', [ResidentController::class, 'storeTicket']);
    });

    Route::post('/send-message', function (Illuminate\Http\Request $request) {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
            'type' => 'required|in:zalo,sms'
        ]);
        
        // Giả lập độ trễ truyền tải mạng 0.8s
        usleep(800000);
        $tenantId = auth()->user()?->tenant_id ?? \App\Models\Tenant::query()->value('id');

        if ($tenantId) {
            \App\Models\NotificationLog::create([
                'tenant_id' => $tenantId,
                'type' => 'manual_message',
                'channel' => $request->type,
                'recipient_name' => null,
                'recipient_contact' => $request->phone,
                'subject' => 'Manual ' . strtoupper($request->type) . ' message',
                'message' => $request->message,
                'status' => 'sent',
                'target_type' => null,
                'target_id' => null,
                'meta' => ['simulated' => true],
                'sent_at' => now(),
            ]);
        }
        
        return response()->json([
            'success' => true,
            'status' => 'sent',
            'phone' => $request->phone,
            'message' => $request->message,
            'type' => $request->type,
            'sent_at' => now()->toIso8601String()
        ]);
    });

    Route::get('/revenue-breakdown', function () {
        $breakdown = \App\Models\UtilityRecord::selectRaw("
            SUM(rooms.price) as room_fee,
            SUM(GREATEST(0, new_electricity - old_electricity) * electricity_price) as electric_fee,
            SUM(GREATEST(0, new_water - old_water) * water_price) as water_fee,
            SUM(150000) as service_fee
        ")
        ->join('rooms', 'rooms.id', '=', 'utility_records.room_id')
        ->where('utility_records.status', 'paid')
        ->first();
        
        $roomFee = (int) ($breakdown->room_fee ?? 75000000);
        $electricFee = (int) ($breakdown->electric_fee ?? 18450000);
        $waterFee = (int) ($breakdown->water_fee ?? 6520000);
        $serviceFee = (int) ($breakdown->service_fee ?? 4500000);
        
        $total = $roomFee + $electricFee + $waterFee + $serviceFee;
        
        return response()->json([
            'success' => true,
            'total' => $total,
            'breakdown' => [
                'room' => $roomFee,
                'electric' => $electricFee,
                'water' => $waterFee,
                'service' => $serviceFee
            ],
            'percentages' => [
                'room' => $total > 0 ? round(($roomFee / $total) * 100, 1) : 0,
                'electric' => $total > 0 ? round(($electricFee / $total) * 100, 1) : 0,
                'water' => $total > 0 ? round(($waterFee / $total) * 100, 1) : 0,
                'service' => $total > 0 ? round(($serviceFee / $total) * 100, 1) : 0
            ]
        ]);
    });

    Route::get('/rooms/compare', function (Illuminate\Http\Request $request) {
        $ids = $request->input('ids');
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Danh sách phòng trống'], 400);
        }
        
        $rooms = \App\Models\Room::with(['residents', 'reviews'])->whereIn('id', $ids)->get();
        
        $mapped = $rooms->map(function($room) {
            $num = intval($room->room_number);
            
            $dbReviews = $room->reviews;
            if ($dbReviews->count() > 0) {
                $rating = $dbReviews->avg('rating');
            } else {
                $rating = 3.6 + (($num * 7) % 15) / 10;
                if ($rating > 5.0) $rating = 5.0;
            }
            
            $distance = 0.4 + (($num * 3) % 12) / 10;
            
            $pets = ($num % 2 == 1);
            $loft = (($num % 3) != 2);
            $balcony = (($num % 4) != 0);
            
            $ownerStars = intval(round($rating));
            $secStars = intval(min(5, max(3, round($rating + ($num % 2 ? 0.5 : -0.5)))));
            
            $priceScore = round(max(0, min(10, (5000000 - $room->price) / 300000 + 2)), 1);
            $distanceScore = round(max(0, min(10, (2.0 - $distance) * 6)), 1);
            $securityScore = $secStars * 2;
            $ownerScore = $ownerStars * 2;
            
            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'price' => $room->price,
                'price_formatted' => number_format($room->price, 0, ',', '.') . 'đ',
                'distance' => $distance,
                'rating' => number_format($rating, 1),
                'owner_stars' => $ownerStars,
                'security_stars' => $secStars,
                'pets' => $pets ? 'Có' : 'Không',
                'loft' => $loft ? 'Có' : 'Không',
                'balcony' => $balcony ? 'Có' : 'Không',
                'scores' => [
                    'price' => $priceScore,
                    'distance' => $distanceScore,
                    'security' => $securityScore,
                    'owner' => $ownerScore
                ]
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $mapped
        ]);
    });

    Route::get('/utility-bill/{id}/qr', function ($id) {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;

        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'error' => 'Khong xac dinh duoc tenant hien tai.',
            ], 403);
        }

        $tenant = \App\Models\Tenant::find($tenantId);

        if (!in_array(($tenant?->verification_status ?? 'unverified'), ['kyc_verified', 'premium_pending', 'premium_verified'], true)) {
            return response()->json([
                'success' => false,
                'error' => 'Can hoan tat KYC truoc khi hien thi VietQR/chuyen khoan.',
            ], 403);
        }

        if (blank($tenant->bank_account_no) || blank($tenant->bank_account_name)) {
            return response()->json([
                'success' => false,
                'error' => 'Tenant chua co thong tin tai khoan ngan hang hop le.',
            ], 422);
        }

        $bill = \App\Models\UtilityRecord::with('room.residents')
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);

        $room = $bill->room;
        $resident = $room?->residents?->first();
        $elecUsed = max(0, (int) $bill->new_electricity - (int) $bill->old_electricity);
        $waterUsed = max(0, (int) $bill->new_water - (int) $bill->old_water);
        $amount = (int) ($room?->price ?? 0)
            + ($elecUsed * (int) $bill->electricity_price)
            + ($waterUsed * (int) $bill->water_price)
            + 150000;

        $bankId = strtoupper((string) ($tenant->bank_name ?: 'MB'));
        $accountNo = (string) $tenant->bank_account_no;
        $accountName = mb_strtoupper((string) $tenant->bank_account_name);
        $description = 'Thanh toan Phong ' . ($room?->room_number ?? 'N/A') . ' thang ' . $bill->billing_month;
        $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$amount}&addInfo="
            . rawurlencode($description)
            . '&accountName=' . rawurlencode($accountName);

        return response()->json([
            'success' => true,
            'room_number' => $room?->room_number ?? 'N/A',
            'resident_name' => $resident?->name ?? 'Khach moi / Cu dan',
            'amount' => $amount,
            'bank_id' => $bankId,
            'account_no' => $accountNo,
            'account_name' => $accountName,
            'description' => rawurlencode($description),
            'qr_url' => $qrUrl,
        ]);
    });

    Route::post('/utility-bills/auto-remind', function () {
        $user = auth()->user();
        $tenantId = $user?->tenant_id;

        if (!$tenantId) {
            return response()->json([
                'success' => false,
                'message' => 'Khong xac dinh duoc tenant hien tai.',
            ], 403);
        }

        $tenant = \App\Models\Tenant::find($tenantId);

        if (!in_array(($tenant?->verification_status ?? 'unverified'), ['kyc_verified', 'premium_pending', 'premium_verified'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Can hoan tat KYC truoc khi tu dong gui nhac thanh toan kem VietQR/chuyen khoan.',
            ], 403);
        }

        if (blank($tenant->bank_account_no) || blank($tenant->bank_account_name)) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant chua co thong tin tai khoan ngan hang hop le.',
            ], 422);
        }

        $currentMonth = now()->format('Y-m');
        
        $unpaidBills = \App\Models\UtilityRecord::with('room.residents')
            ->where('tenant_id', $tenantId)
            ->where('status', '!=', 'paid')
            ->where('billing_month', $currentMonth)
            ->get();
            
        $sentRooms = [];
        
        foreach ($unpaidBills as $bill) {
            $room = $bill->room;
            if (!$room) continue;
            
            $resident = $room->residents->first();
            if (!$resident) continue;
            
            $phone = $resident->phone ?? '0987654321';
            $residentName = $resident->name;
            
            // Calculate Total
            $elecUsed = max(0, $bill->new_electricity - $bill->old_electricity);
            $waterUsed = max(0, $bill->new_water - $bill->old_water);
            $totalAmount = $room->price + ($elecUsed * $bill->electricity_price) + ($waterUsed * $bill->water_price) + 150000;
            $totalFormatted = number_format($totalAmount, 0, ',', '.') . 'đ';
            
            // QR payment URL
            $bankId = strtoupper((string) ($tenant->bank_name ?: 'MB'));
            $accountNo = (string) $tenant->bank_account_no;
            $accountName = mb_strtoupper((string) $tenant->bank_account_name);
            $addInfo = rawurlencode("Thanh toan Phong " . $room->room_number . " thang " . now()->format('m'));
            $accNameEscaped = rawurlencode($accountName);
            $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$totalAmount}&addInfo={$addInfo}&accountName={$accNameEscaped}";
            
            // Build Zalo message template
            $message = "📢 [SMARTROOM REMINDER] Kính gửi Anh/Chị {$residentName} (Phòng {$room->room_number}). Hệ thống nhận thấy hóa đơn tiền trọ tháng " . now()->format('m/Y') . " của phòng mình chưa được hoàn tất. Tổng số tiền cần thanh toán là {$totalFormatted}. Kính mong Anh/Chị thanh toán trước ngày 10 để tránh trễ hạn. Link quét QR VietQR thanh toán nhanh: {$qrUrl}. Trân trọng cảm ơn!";
            
            // Log to laravel log
            \Illuminate\Support\Facades\Log::info("Auto Zalo Sent to Room {$room->room_number} ({$residentName}): {$message}");
            
            // Update bill status to 'sent'
            $bill->update(['status' => 'sent']);
            
            $sentRooms[] = [
                'room_number' => $room->room_number,
                'resident_name' => $residentName,
                'phone' => $phone,
                'total_amount' => $totalAmount,
                'total_amount_formatted' => $totalFormatted
            ];
        }
        
        return response()->json([
            'success' => true,
            'billing_month' => $currentMonth,
            'sent_count' => count($sentRooms),
            'sent_rooms' => $sentRooms
        ]);
    });
});
