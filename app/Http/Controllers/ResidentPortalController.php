<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Resident;
use App\Models\Ticket;
use App\Models\UtilityRecord;
use App\Services\AiManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResidentPortalController extends Controller
{
    private const SERVICE_FEE = 150000;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập trước.');
            }

            $user = Auth::user();
            if (!$user->isResident()) {
                $route = match (true) {
                    $user->isAdmin() => 'user.list',
                    $user->canAccessLandlordDashboard() => 'smartroom.admin',
                    default => 'renty.user',
                };

                return redirect()->route($route)->with('error', 'Tài khoản của bạn không có quyền truy cập cổng cư dân.');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $resident = $this->currentResident();

        if (!$resident || !$resident->room) {
            return view('resident.portal', [
                'resident' => $resident,
                'room' => null,
                'bills' => collect(),
                'unpaidTotal' => 0,
                'contract' => null,
                'tickets' => collect(),
                'statusLabels' => $this->ticketStatusLabels(),
            ]);
        }

        $room = $resident->room;
        $bills = UtilityRecord::with('room')
            ->where('room_id', $room->id)
            ->orderByDesc('billing_month')
            ->get()
            ->map(fn (UtilityRecord $record) => $this->decorateBill($record));

        $contract = Contract::where('resident_id', $resident->id)
            ->where('room_id', $room->id)
            ->orderByRaw("case when status = 'active' then 0 when status = 'pending' then 1 else 2 end")
            ->orderByDesc('end_date')
            ->first();

        $tickets = Ticket::where('resident_id', $resident->id)
            ->where('room_id', $room->id)
            ->latest()
            ->get();

        return view('resident.portal', [
            'resident' => $resident,
            'room' => $room,
            'bills' => $bills,
            'unpaidTotal' => $bills->where('status', '!=', 'paid')->sum('total_amount'),
            'contract' => $contract,
            'tickets' => $tickets,
            'statusLabels' => $this->ticketStatusLabels(),
        ]);
    }

    public function storeTicket(Request $request)
    {
        $resident = $this->currentResident();
        if (!$resident || !$resident->room) {
            return back()->with('error', 'Tài khoản chưa được gán phòng.');
        }

        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $input[$key] = strip_tags(trim(preg_replace('/\s+/u', ' ', $value)));
            }
        }
        $request->merge($input);

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string|max:1000',
            'category' => 'required|in:electric,water,furniture,maintenance,other',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = '/storage/' . $request->file('image')->store('tickets', 'public');
        }

        Ticket::create([
            'tenant_id' => $resident->tenant_id,
            'room_id' => $resident->room_id,
            'resident_id' => $resident->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'image_path' => $imagePath,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('smartroom.resident', ['tab' => 'tickets'])
            ->with('success', 'Đã gửi yêu cầu sửa chữa. Ban quản lý sẽ xử lý sớm.');
    }

    public function analyzeTicket(Request $request, AiManagementService $aiManagementService)
    {
        $resident = $this->currentResident();
        if (!$resident || !$resident->room) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:150',
            'description' => 'required|string|min:5|max:1000',
        ]);

        return response()->json([
            'success' => true,
            'analysis' => $aiManagementService->analyzeMaintenanceTicket(
                strip_tags($validated['description']),
                isset($validated['title']) ? strip_tags($validated['title']) : null
            ),
        ]);
    }

    public function billQr($id)
    {
        if (!is_numeric($id) || (int) $id <= 0) {
            abort(404);
        }

        $resident = $this->currentResident();
        if (!$resident || !$resident->room) {
            abort(404);
        }

        if (!in_array(($resident->tenant?->verification_status ?? 'unverified'), ['kyc_verified', 'premium_pending', 'premium_verified'], true)) {
            return redirect()
                ->route('smartroom.resident')
                ->with('error', 'Chủ trọ đang hoàn tất xác minh nhận tiền. Vui lòng liên hệ ban quản lý để nhận hướng dẫn thanh toán.');
        }

        $record = UtilityRecord::where('room_id', $resident->room_id)->findOrFail($id);
        $bill = $this->decorateBill($record);

        return view('resident.qr', [
            'resident' => $resident,
            'bill' => $bill,
            'qrUrl' => $this->vietQrUrl($resident->room->room_number, $bill->billing_month, $bill->total_amount),
        ]);
    }

    private function currentResident(): ?Resident
    {
        $user = Auth::user();

        return Resident::with(['room.building', 'tenant'])
            ->where('status', 'active')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);

                if ($user->phone) {
                    $query->orWhere('phone', $user->phone);
                }

                if ($user->email) {
                    $query->orWhere('email', $user->email);
                }
            })
            ->first();
    }

    private function decorateBill(UtilityRecord $record): object
    {
        $electricityUsage = max(0, (int) $record->new_electricity - (int) $record->old_electricity);
        $waterUsage = max(0, (int) $record->new_water - (int) $record->old_water);
        $roomAmount = (int) optional($record->room)->price;
        $electricityAmount = $electricityUsage * (int) $record->electricity_price;
        $waterAmount = $waterUsage * (int) $record->water_price;
        $totalAmount = $roomAmount + $electricityAmount + $waterAmount + self::SERVICE_FEE;

        return (object) [
            'id' => $record->id,
            'billing_month' => $record->billing_month,
            'status' => $record->status,
            'status_label' => $this->billStatusLabels()[$record->status] ?? $record->status,
            'room_amount' => $roomAmount,
            'electricity_usage' => $electricityUsage,
            'electricity_amount' => $electricityAmount,
            'water_usage' => $waterUsage,
            'water_amount' => $waterAmount,
            'service_amount' => self::SERVICE_FEE,
            'total_amount' => $totalAmount,
            'payment_date' => $record->payment_date,
            'payment_method' => $record->payment_method,
        ];
    }

    private function vietQrUrl(string $roomNumber, string $billingMonth, int $amount): string
    {
        $bankId = 'MB';
        $accountNo = '9999888889999';
        $accountName = rawurlencode('NGUYEN VAN CHU NHA');
        $addInfo = rawurlencode('Thanh toan phong ' . $roomNumber . ' thang ' . $billingMonth);

        return "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$amount}&addInfo={$addInfo}&accountName={$accountName}";
    }

    private function billStatusLabels(): array
    {
        return [
            'sent' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            'overdue' => 'Quá hạn',
        ];
    }

    private function ticketStatusLabels(): array
    {
        return [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'resolved' => 'Đã hoàn tất',
        ];
    }
}
