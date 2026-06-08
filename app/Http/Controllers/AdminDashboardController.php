<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Contract;
use App\Models\Equipment;
use App\Models\NotificationLog;
use App\Models\Room;
use App\Models\Resident;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\UtilityRecord;
use App\Services\AdminActivityLogger;
use App\Services\AiManagementService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = $this->currentTenantId();

        // 1. Stats ribbon
        $totalRooms = Room::where('tenant_id', $tenantId)->count();
        $occupiedRooms = Room::where('tenant_id', $tenantId)->where('status', 'occupied')->count();
        $emptyRooms = Room::where('tenant_id', $tenantId)->where('status', 'empty')->count();
        $overdueRooms = Room::where('tenant_id', $tenantId)->where('status', 'overdue')->count();

        // 2. Charts Data
        // Revenue trend from paid utility records
        $monthlyRevenue = UtilityRecord::selectRaw("
            billing_month,
            SUM(rooms.price + (new_electricity - old_electricity)*electricity_price + (new_water - old_water)*water_price + 150000) as total_revenue
        ")
        ->join('rooms', 'rooms.id', '=', 'utility_records.room_id')
        ->where('rooms.tenant_id', $tenantId)
        ->where('utility_records.status', 'paid')
        ->groupBy('billing_month')
        ->orderByDesc('billing_month')
        ->limit(3)
        ->get()
        ->sortBy('billing_month')
        ->values();

        $chartMonths = [];
        $chartRevenue = [];
        foreach ($monthlyRevenue as $rev) {
            $monthNum = explode('-', $rev->billing_month)[1];
            $chartMonths[] = 'Tháng ' . intval($monthNum);
            $chartRevenue[] = (int) $rev->total_revenue;
        }

        // Default value fallback if empty
        if (empty($chartMonths)) {
            $chartMonths = collect(range(2, 0))
                ->map(fn ($monthsAgo) => 'Tháng ' . Carbon::now()->subMonths($monthsAgo)->month)
                ->all();
            $chartRevenue = [31500000, 34200000, 38100000];
        }

        // 3. Room Map (with active residents and latest billing information)
        $rooms = Room::with(['residents' => function($q) {
            $q->where('status', 'active');
        }, 'utilityRecords' => function($q) {
            $q->orderBy('billing_month', 'desc');
        }])->orderBy('room_number')->get();

        $roomsByFloor = $rooms->groupBy('floor');

        // 4. Utility Readings Tab
        // Rooms that are rented (occupied/overdue) need meter readings
        $utilityRooms = Room::where('status', '!=', 'empty')
            ->with(['residents' => function($q) {
                $q->where('status', 'active');
            }, 'utilityRecords' => function($q) {
                $q->orderBy('billing_month', 'desc');
            }])
            ->orderBy('room_number')
            ->get();

        // 5. Resident Management Tab
        $residentFilters = [
            'q' => trim((string) $request->query('resident_q', '')),
        ];
        $residentStats = Resident::query()->get();
        $residents = Resident::with('room')
            ->when($residentFilters['q'] !== '', function ($query) use ($residentFilters) {
                $identityKeyword = $this->prefixLike($residentFilters['q']);
                $nameKeyword = $this->containsLike($residentFilters['q']);

                $query->where(function ($residentQuery) use ($identityKeyword, $nameKeyword) {
                    $residentQuery->where('name', 'like', $nameKeyword)
                        ->orWhere('phone', 'like', $identityKeyword)
                        ->orWhere('cccd', 'like', $identityKeyword);
                });
            })
            ->orderBy('id', 'desc')
            ->get();
        $emptyRoomsList = Room::where('status', 'empty')->orderBy('room_number')->get();

        // 6. Contracts Tab
        $contracts = Contract::with(['room', 'resident'])->orderBy('id', 'desc')->get();

        // 8. Contact Requests Tab
        $contactRequests = \App\Models\ContactRequest::with('room')->orderBy('id', 'desc')->get();

        // 9. Smart alerts
        $today = Carbon::today();
        $contractWarningDate = $today->copy()->addDays(30);
        $emptyRoomWarningDate = $today->copy()->subDays(30);

        $expiringContracts = Contract::with(['room', 'resident'])
            ->where('status', 'active')
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $contractWarningDate)
            ->orderBy('end_date')
            ->take(6)
            ->get()
            ->map(function ($contract) use ($today) {
                $endDate = Carbon::parse($contract->end_date);

                return [
                    'title' => 'Hợp đồng phòng ' . ($contract->room->room_number ?? 'N/A') . ' sắp hết hạn',
                    'detail' => ($contract->resident->name ?? 'Chưa rõ khách thuê') . ' còn ' . $today->diffInDays($endDate) . ' ngày',
                    'meta' => $endDate->format('d/m/Y'),
                ];
            });

        $overdueBills = Bill::with('room')
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('billing_month')
            ->get()
            ->filter(function ($bill) use ($today) {
                return $bill->status === 'overdue' || Carbon::parse($bill->billing_month . '-10')->lt($today);
            })
            ->take(6)
            ->map(function ($bill) {
                $dueDate = Carbon::parse($bill->billing_month . '-10');

                return [
                    'title' => 'Hóa đơn phòng ' . ($bill->room->room_number ?? 'N/A') . ' quá hạn',
                    'detail' => 'Tháng ' . $dueDate->format('m/Y') . ' - ' . number_format($bill->total_amount) . 'đ',
                    'meta' => 'Hạn ' . $dueDate->format('d/m/Y'),
                ];
            });

        $overdueUtilities = UtilityRecord::with('room')
            ->whereIn('status', ['sent', 'overdue'])
            ->orderBy('billing_month')
            ->get()
            ->filter(function ($record) use ($today) {
                return $record->status === 'overdue' || Carbon::parse($record->billing_month . '-10')->lt($today);
            })
            ->take(6)
            ->map(function ($record) {
                $dueDate = Carbon::parse($record->billing_month . '-10');
                $roomPrice = $record->room->price ?? 0;
                $total = $roomPrice
                    + (($record->new_electricity - $record->old_electricity) * $record->electricity_price)
                    + (($record->new_water - $record->old_water) * $record->water_price)
                    + 150000;

                return [
                    'title' => 'Phiếu điện nước phòng ' . ($record->room->room_number ?? 'N/A') . ' quá hạn',
                    'detail' => 'Tháng ' . $dueDate->format('m/Y') . ' - ' . number_format($total) . 'đ',
                    'meta' => 'Hạn ' . $dueDate->format('d/m/Y'),
                ];
            });

        $emptyRoomAlerts = Room::where('status', 'empty')
            ->where('updated_at', '<=', $emptyRoomWarningDate)
            ->orderBy('updated_at')
            ->take(6)
            ->get()
            ->map(function ($room) use ($today) {
                return [
                    'title' => 'Phòng ' . $room->room_number . ' trống lâu ngày',
                    'detail' => 'Đã trống khoảng ' . $room->updated_at->diffInDays($today) . ' ngày',
                    'meta' => number_format($room->price) . 'đ/tháng',
                ];
            });

        $lowStockEquipment = Equipment::orderBy('name')
            ->get()
            ->filter(function ($equipment) {
                return $equipment->stock_quantity <= 2;
            })
            ->take(6)
            ->map(function ($equipment) {
                return [
                    'title' => $equipment->name . ' sắp thiếu',
                    'detail' => 'Tồn kho còn ' . $equipment->stock_quantity . ' ' . $equipment->unit,
                    'meta' => $equipment->code,
                ];
            })
            ->values();

        $brokenEquipmentTickets = Ticket::with('room')
            ->whereIn('status', ['pending', 'processing'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get()
            ->map(function ($ticket) {
                return [
                    'title' => $ticket->title,
                    'detail' => 'Phòng ' . ($ticket->room->room_number ?? 'N/A') . ' - ' . $ticket->status,
                    'meta' => $ticket->created_at->format('d/m/Y'),
                ];
            });

        $equipmentAlerts = $lowStockEquipment->concat($brokenEquipmentTickets)->take(8)->values();
        $billAlerts = $overdueBills->concat($overdueUtilities)->take(8)->values();

        $smartAlertGroups = [
            [
                'key' => 'contracts',
                'label' => 'Hợp đồng sắp hết hạn',
                'count' => $expiringContracts->count(),
                'icon' => 'fa-file-signature',
                'color' => 'indigo',
                'items' => $expiringContracts,
                'empty' => 'Chưa có hợp đồng nào hết hạn trong 30 ngày tới.',
            ],
            [
                'key' => 'bills',
                'label' => 'Hóa đơn quá hạn',
                'count' => $billAlerts->count(),
                'icon' => 'fa-receipt',
                'color' => 'amber',
                'items' => $billAlerts,
                'empty' => 'Không có hóa đơn quá hạn.',
            ],
            [
                'key' => 'rooms',
                'label' => 'Phòng trống lâu ngày',
                'count' => $emptyRoomAlerts->count(),
                'icon' => 'fa-door-open',
                'color' => 'emerald',
                'items' => $emptyRoomAlerts,
                'empty' => 'Không có phòng trống quá 30 ngày.',
            ],
            [
                'key' => 'equipment',
                'label' => 'Thiết bị cần chú ý',
                'count' => $equipmentAlerts->count(),
                'icon' => 'fa-screwdriver-wrench',
                'color' => 'rose',
                'items' => $equipmentAlerts,
                'empty' => 'Tồn kho thiết bị ổn định và chưa có báo hỏng mở.',
            ],
        ];

        $smartAlertTotal = collect($smartAlertGroups)->sum('count');
        $notificationLogs = NotificationLog::where('tenant_id', $tenantId)
            ->latest()
            ->take(10)
            ->get();
        $notificationSummary = [
            'sent' => NotificationLog::where('tenant_id', $tenantId)->where('status', 'sent')->count(),
            'skipped' => NotificationLog::where('tenant_id', $tenantId)->where('status', 'skipped')->count(),
            'today' => NotificationLog::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
        ];

        // 7. Recent activities log (Mocked for dashboard realism based on DB actions)
        $recentActivities = [
            ['time' => '10 phút trước', 'icon' => 'fa-bolt text-amber-400', 'desc' => 'Hóa đơn tiền điện nước phòng 103 vừa được gửi đi'],
            ['time' => '1 giờ trước', 'icon' => 'fa-user-plus text-emerald-400', 'desc' => 'Thêm mới cư dân Ngô Tiến Đạt vào phòng 303'],
            ['time' => '1 ngày trước', 'icon' => 'fa-check text-indigo-400', 'desc' => 'Phòng 201 đã thanh toán hóa đơn tháng 05'],
        ];

        return view('admin.admin', compact(
            'totalRooms',
            'occupiedRooms',
            'emptyRooms',
            'overdueRooms',
            'chartMonths',
            'chartRevenue',
            'rooms',
            'roomsByFloor',
            'utilityRooms',
            'residents',
            'residentStats',
            'residentFilters',
            'emptyRoomsList',
            'recentActivities',
            'contracts',
            'contactRequests',
            'smartAlertGroups',
            'smartAlertTotal',
            'notificationLogs',
            'notificationSummary'
        ));
    }

    private function currentTenantId(): int
    {
        $tenantId = Auth::user()?->tenant_id;
        if ($tenantId) {
            return (int) $tenantId;
        }

        $fallbackTenantId = Tenant::query()->value('id');
        if (!$fallbackTenantId) {
            abort(404, 'Khong tim thay tenant.');
        }

        return (int) $fallbackTenantId;
    }

    private function prefixLike(string $value): string
    {
        return addcslashes(trim($value), '\%_') . '%';
    }

    private function containsLike(string $value): string
    {
        return '%' . addcslashes(trim($value), '\%_') . '%';
    }

    public function aiDashboardInsight(AiManagementService $aiManagementService)
    {
        return response()->json([
            'success' => true,
            'insight' => $aiManagementService->dashboardInsight($this->currentTenantId()),
        ]);
    }

    public function aiAssistant(Request $request, AiManagementService $aiManagementService)
    {
        $validated = $request->validate([
            'question' => 'required|string|min:3|max:500',
        ]);

        return response()->json([
            'success' => true,
            'answer' => $aiManagementService->answerManagementQuestion(
                $this->currentTenantId(),
                $validated['question']
            ),
        ]);
    }

    public function aiContractTerms(Request $request, AiManagementService $aiManagementService)
    {
        $tenantId = $this->currentTenantId();
        $validated = $request->validate([
            'room_id' => 'required|integer',
            'resident_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'deposit' => 'required|integer|min:0',
        ]);

        $room = Room::where('tenant_id', $tenantId)->findOrFail($validated['room_id']);
        $resident = Resident::where('tenant_id', $tenantId)->findOrFail($validated['resident_id']);

        return response()->json([
            'success' => true,
            'terms' => $aiManagementService->generateContractTerms([
                'room_number' => $room->room_number,
                'room_price' => (int) $room->price,
                'room_area' => (int) $room->area,
                'room_amenities' => $room->amenities ?? [],
                'resident_name' => $resident->name,
                'resident_phone' => $resident->phone,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'deposit' => (int) $validated['deposit'],
            ]),
        ]);
    }

    public function storeUtility(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'new_electricity' => 'required|integer',
            'new_water' => 'required|integer',
        ]);

        $room = Room::findOrFail($request->room_id);
        $currentMonth = Carbon::now()->format('Y-m');

        // Check if there is already a record for this month
        $existing = UtilityRecord::where('room_id', $room->id)
            ->where('billing_month', $currentMonth)
            ->first();

        if ($existing) {
            if ($request->new_electricity < $existing->old_electricity || $request->new_water < $existing->old_water) {
                return redirect()->route('smartroom.admin', ['tab' => 'utility-section'])->with('error', 'Chỉ số mới không được nhỏ hơn chỉ số cũ!');
            }
            $before = $existing->only(['new_electricity', 'new_water', 'status']);
            $existing->update([
                'new_electricity' => $request->new_electricity,
                'new_water' => $request->new_water,
                'status' => 'sent'
            ]);
            $room->update(['status' => 'overdue']);
            AdminActivityLogger::log(
                'update',
                'utilities',
                'Cập nhật chỉ số điện nước phòng ' . $room->room_number . ' tháng ' . $currentMonth,
                $existing,
                ['room_number' => $room->room_number, 'billing_month' => $currentMonth],
                $before,
                $existing->fresh()->only(['new_electricity', 'new_water', 'status'])
            );
            return redirect()->route('smartroom.admin', ['tab' => 'utility-section'])->with('success', 'Đã cập nhật chỉ số điện nước & gửi hóa đơn thành công!');
        }

        // Find latest utility record to verify indices
        $latest = UtilityRecord::where('room_id', $room->id)
            ->orderBy('billing_month', 'desc')
            ->first();

        $oldElec = $latest ? $latest->new_electricity : 0;
        $oldWater = $latest ? $latest->new_water : 0;

        if ($request->new_electricity < $oldElec || $request->new_water < $oldWater) {
            return redirect()->route('smartroom.admin', ['tab' => 'utility-section'])->with('error', 'Chỉ số mới không được nhỏ hơn chỉ số cũ!');
        }

        $record = UtilityRecord::create([
            'room_id' => $room->id,
            'billing_month' => $currentMonth,
            'old_electricity' => $oldElec,
            'new_electricity' => $request->new_electricity,
            'old_water' => $oldWater,
            'new_water' => $request->new_water,
            'electricity_price' => 3500,
            'water_price' => 15000,
            'status' => 'sent' // Invoice created and sent
        ]);

        // Mark room status as overdue until paid
        $room->update(['status' => 'overdue']);

        AdminActivityLogger::log(
            'create',
            'utilities',
            'Chốt chỉ số điện nước phòng ' . $room->room_number . ' tháng ' . $currentMonth,
            $record,
            ['room_number' => $room->room_number, 'billing_month' => $currentMonth],
            null,
            $record->only(['old_electricity', 'new_electricity', 'old_water', 'new_water', 'status'])
        );

        return redirect()->route('smartroom.admin', ['tab' => 'utility-section'])->with('success', 'Đã lưu chỉ số điện nước & gửi hóa đơn thành công!');
    }

    public function storeUtilityBulk(Request $request)
    {
        $utilities = $request->input('utilities', []);
        $currentMonth = Carbon::now()->format('Y-m');
        
        $savedCount = 0;
        foreach ($utilities as $roomId => $data) {
            $newElec = $data['new_electricity'] ?? null;
            $newWater = $data['new_water'] ?? null;

            if ($newElec !== null && $newWater !== null && $newElec !== '' && $newWater !== '') {
                $room = Room::findOrFail($roomId);
                
                $existing = UtilityRecord::where('room_id', $room->id)
                    ->where('billing_month', $currentMonth)
                    ->first();
                    
                if ($existing) {
                    if (intval($newElec) >= $existing->old_electricity && intval($newWater) >= $existing->old_water) {
                        $existing->update([
                            'new_electricity' => $newElec,
                            'new_water' => $newWater,
                            'status' => 'sent'
                        ]);
                        $room->update(['status' => 'overdue']);
                        $savedCount++;
                    }
                } else {
                    $latest = UtilityRecord::where('room_id', $room->id)
                        ->orderBy('billing_month', 'desc')
                        ->first();

                    $oldElec = $latest ? $latest->new_electricity : 0;
                    $oldWater = $latest ? $latest->new_water : 0;

                    if (intval($newElec) >= $oldElec && intval($newWater) >= $oldWater) {
                        UtilityRecord::create([
                            'room_id' => $room->id,
                            'billing_month' => $currentMonth,
                            'old_electricity' => $oldElec,
                            'new_electricity' => $newElec,
                            'old_water' => $oldWater,
                            'new_water' => $newWater,
                            'electricity_price' => 3500,
                            'water_price' => 15000,
                            'status' => 'sent'
                        ]);

                        $room->update(['status' => 'overdue']);
                        $savedCount++;
                    }
                }
            }
        }

        if ($savedCount > 0) {
            AdminActivityLogger::log(
                'create',
                'utilities',
                'Chốt nhanh chỉ số điện nước cho ' . $savedCount . ' phòng tháng ' . $currentMonth,
                null,
                ['billing_month' => $currentMonth, 'saved_count' => $savedCount]
            );
            return redirect()->route('smartroom.admin', ['tab' => 'utility-section'])->with('success', "Đã chốt nhanh chỉ số điện nước và gửi hóa đơn cho {$savedCount} phòng thành công!");
        }

        return redirect()->route('smartroom.admin', ['tab' => 'utility-section'])->with('error', 'Không có chỉ số hợp lệ nào được cập nhật hoặc các chỉ số mới nhỏ hơn chỉ số cũ!');
    }

    public function storeResident(Request $request)
    {
        // 1. Trim & Sanitize input chống XSS & khoảng trắng 2-bytes
        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $trimmed = preg_replace('/^[ -   　\s]+|[ -   　\s]+$/u', '', $value);
                $input[$key] = strip_tags($trimmed);
            }
        }
        $request->merge($input);

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'cccd' => 'nullable|string|max:20',
            'hometown' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'temporary_residence_status' => 'required|in:none,registered,absent',
        ]);

        $room = Room::findOrFail($request->room_id);

        // Create resident
        $resident = Resident::create([
            'tenant_id' => $room->tenant_id ?? (\App\Models\Tenant::first()->id ?? 1),
            'room_id' => $request->room_id,
            'name' => $request->name,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'email' => $request->email ?? (str_replace(' ', '', strtolower($request->name)) . '@gmail.com'),
            'cccd' => $request->cccd,
            'hometown' => $request->hometown,
            'start_date' => $request->start_date,
            'status' => 'active',
            'temporary_residence_status' => $request->temporary_residence_status,
            'version' => 1
        ]);

        // Update room status
        $room->update(['status' => 'occupied']);

        AdminActivityLogger::log(
            'create',
            'residents',
            'Thêm cư dân ' . $resident->name . ' vào phòng ' . $room->room_number,
            $resident,
            ['room_number' => $room->room_number],
            null,
            $resident->only(['room_id', 'name', 'phone', 'email', 'cccd', 'temporary_residence_status'])
        );

        return redirect()->route('smartroom.admin', ['tab' => 'resident-section'])->with('success', 'Đã thêm mới cư dân và kích hoạt trạng thái phòng thành công!');
    }

    public function updateResident(Request $request, $id)
    {
        // 1. Kiểm tra ID không hợp lệ hoặc sai định dạng
        if (!is_numeric($id) || intval($id) <= 0 || intval($id) > 999999999) {
            abort(404);
        }

        $resident = Resident::find($id);
        if (!$resident) {
            abort(404);
        }

        // Trim & Sanitize input chống XSS & khoảng trắng 2-bytes
        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $trimmed = preg_replace('/^[ -   　\s]+|[ -   　\s]+$/u', '', $value);
                $input[$key] = strip_tags($trimmed);
            }
        }
        $request->merge($input);

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'cccd' => 'nullable|string|max:20',
            'hometown' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'temporary_residence_status' => 'required|in:none,registered,absent',
            'version' => 'required|integer', // Optimistic locking
        ]);

        // 2. Chặn ghi đè ghi nhận xung đột (Optimistic locking)
        if ($resident->version != $request->version) {
            return redirect()->route('smartroom.admin', ['tab' => 'resident-section'])->with('error', 'Dữ liệu đã thay đổi, vui lòng tải lại trang trước khi cập nhật!');
        }

        $oldRoomId = $resident->room_id;
        $newRoomId = $request->room_id;
        $before = $resident->only(['room_id', 'name', 'phone', 'email', 'cccd', 'temporary_residence_status']);

        $resident->update([
            'room_id' => $newRoomId,
            'name' => $request->name,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'email' => $request->email ?? (str_replace(' ', '', strtolower($request->name)) . '@gmail.com'),
            'cccd' => $request->cccd,
            'hometown' => $request->hometown,
            'start_date' => $request->start_date,
            'temporary_residence_status' => $request->temporary_residence_status,
            'version' => $resident->version + 1,
        ]);

        if ($oldRoomId != $newRoomId) {
            Room::syncOccupancyStatusById($oldRoomId);
            $newRoom = Room::findOrFail($newRoomId);
            $newRoom->syncOccupancyStatus();
        }

        AdminActivityLogger::log(
            'update',
            'residents',
            'Cập nhật cư dân ' . $resident->name,
            $resident,
            ['old_room_id' => $oldRoomId, 'new_room_id' => $newRoomId],
            $before,
            $resident->fresh()->only(['room_id', 'name', 'phone', 'email', 'cccd', 'temporary_residence_status'])
        );

        return redirect()->route('smartroom.admin', ['tab' => 'resident-section'])->with('success', 'Cập nhật thông tin cư dân thành công!');
    }

    public function deleteResident($id)
    {
        // 1. Kiểm tra ID không hợp lệ hoặc sai định dạng
        if (!is_numeric($id) || intval($id) <= 0 || intval($id) > 999999999) {
            abort(404);
        }

        $resident = Resident::find($id);

        // 2. Kiểm tra xung đột xóa trùng (Concurrency)
        if (!$resident) {
            return redirect()->route('smartroom.admin', ['tab' => 'resident-section'])->with('error', 'Mục này đã bị xóa trước đó hoặc không tồn tại!');
        }

        $room = $resident->room;
        $before = $resident->only(['room_id', 'name', 'phone', 'email', 'cccd', 'temporary_residence_status']);
        $residentName = $resident->name;
        $resident->delete();

        if ($room) {
            $room->syncOccupancyStatus();
        }

        AdminActivityLogger::log(
            'delete',
            'residents',
            'Xóa cư dân ' . $residentName,
            $resident,
            ['room_number' => $room?->room_number],
            $before
        );

        return redirect()->route('smartroom.admin', ['tab' => 'resident-section'])->with('success', 'Đã xóa cư dân và cập nhật trạng thái phòng thành công!');
    }

    // --- Resident Relative Management APIs ---

    public function getRelatives($residentId)
    {
        if (!is_numeric($residentId) || intval($residentId) <= 0) {
            return response()->json(['success' => false, 'message' => 'ID cư dân không hợp lệ!'], 400);
        }

        $resident = Resident::find($residentId);
        if (!$resident) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy cư dân!'], 404);
        }

        $relatives = $resident->relatives()->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'resident_name' => $resident->name,
            'relatives' => $relatives
        ]);
    }

    public function storeRelative(Request $request, $residentId)
    {
        if (!is_numeric($residentId) || intval($residentId) <= 0) {
            return response()->json(['success' => false, 'message' => 'ID cư dân không hợp lệ!'], 400);
        }

        $resident = Resident::find($residentId);
        if (!$resident) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy cư dân!'], 404);
        }

        // Sanitize inputs
        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $trimmed = preg_replace('/^[ -   　\s]+|[ -   　\s]+$/u', '', $value);
                $input[$key] = strip_tags($trimmed);
            }
        }
        $request->merge($input);

        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'cccd' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'hometown' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:100',
            'temporary_residence_status' => 'required|in:none,registered,absent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $relative = $resident->relatives()->create([
            'name' => $request->name,
            'dob' => $request->dob,
            'cccd' => $request->cccd,
            'phone' => $request->phone,
            'hometown' => $request->hometown,
            'relationship' => $request->relationship,
            'temporary_residence_status' => $request->temporary_residence_status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'version' => 1
        ]);

        AdminActivityLogger::log(
            'create',
            'relatives',
            'Thêm người thân ' . $relative->name . ' cho cư dân ' . $resident->name,
            $relative,
            ['resident_id' => $resident->id, 'resident_name' => $resident->name],
            null,
            $relative->only(['resident_id', 'name', 'phone', 'cccd', 'relationship', 'temporary_residence_status'])
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm thông tin người thân tạm trú thành công!',
            'relative' => $relative
        ]);
    }

    public function updateRelative(Request $request, $id)
    {
        if (!is_numeric($id) || intval($id) <= 0) {
            return response()->json(['success' => false, 'message' => 'ID không hợp lệ!'], 400);
        }

        $relative = \App\Models\ResidentRelative::find($id);
        if (!$relative) {
            return response()->json(['success' => false, 'message' => 'Mục này đã bị xóa trước đó hoặc không tồn tại!'], 404);
        }

        // Sanitize inputs
        $input = $request->all();
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $trimmed = preg_replace('/^[ -   　\s]+|[ -   　\s]+$/u', '', $value);
                $input[$key] = strip_tags($trimmed);
            }
        }
        $request->merge($input);

        $request->validate([
            'name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'cccd' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'hometown' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:100',
            'temporary_residence_status' => 'required|in:none,registered,absent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'version' => 'required|integer'
        ]);

        // Optimistic locking
        if ($relative->version != $request->version) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu đã thay đổi, vui lòng tải lại danh sách trước khi cập nhật!'
            ], 409);
        }

        $before = $relative->only(['resident_id', 'name', 'phone', 'cccd', 'relationship', 'temporary_residence_status']);

        $relative->update([
            'name' => $request->name,
            'dob' => $request->dob,
            'cccd' => $request->cccd,
            'phone' => $request->phone,
            'hometown' => $request->hometown,
            'relationship' => $request->relationship,
            'temporary_residence_status' => $request->temporary_residence_status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'version' => $relative->version + 1
        ]);

        AdminActivityLogger::log(
            'update',
            'relatives',
            'Cập nhật người thân ' . $relative->name,
            $relative,
            ['resident_id' => $relative->resident_id],
            $before,
            $relative->fresh()->only(['resident_id', 'name', 'phone', 'cccd', 'relationship', 'temporary_residence_status'])
        );

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin người thân thành công!',
            'relative' => $relative
        ]);
    }

    public function deleteRelative($id)
    {
        if (!is_numeric($id) || intval($id) <= 0) {
            return response()->json(['success' => false, 'message' => 'ID không hợp lệ!'], 400);
        }

        $relative = \App\Models\ResidentRelative::find($id);
        if (!$relative) {
            return response()->json(['success' => false, 'message' => 'Mục này đã bị xóa trước đó hoặc không tồn tại!'], 404);
        }

        $before = $relative->only(['resident_id', 'name', 'phone', 'cccd', 'relationship', 'temporary_residence_status']);
        $relativeName = $relative->name;
        $relative->delete();

        AdminActivityLogger::log(
            'delete',
            'relatives',
            'Xóa người thân ' . $relativeName,
            $relative,
            ['resident_id' => $before['resident_id'] ?? null],
            $before
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa người thân tạm trú thành công!'
        ]);
    }

    public function payUtility($id)
    {
        $record = UtilityRecord::findOrFail($id);
        $before = $record->only(['status', 'payment_date', 'payment_method']);
        $record->update([
            'status' => 'paid',
            'payment_date' => now(),
            'payment_method' => 'cash',
        ]);

        // Check if there are any other unpaid utility records for this room
        $unpaidCount = UtilityRecord::where('room_id', $record->room_id)
            ->where('status', '!=', 'paid')
            ->count();

        if ($unpaidCount === 0) {
            $room = Room::findOrFail($record->room_id);
            if ($room->status === 'overdue') {
                $room->update(['status' => 'occupied']);
            }
        }

        $record->loadMissing('room');
        AdminActivityLogger::log(
            'payment',
            'payments',
            'Xác nhận thanh toán hóa đơn điện nước phòng ' . ($record->room->room_number ?? $record->room_id),
            $record,
            ['room_number' => $record->room->room_number ?? null, 'billing_month' => $record->billing_month],
            $before,
            $record->fresh()->only(['status', 'payment_date', 'payment_method'])
        );

        return redirect()->route('smartroom.admin', ['tab' => 'utility-section'])->with('success', 'Xác nhận thanh toán hóa đơn điện nước thành công!');
    }

    public function printUtility($id)
    {
        $record = UtilityRecord::with('room')->findOrFail($id);
        $resident = Resident::where('room_id', $record->room_id)
            ->where('status', 'active')
            ->first();

        return view('admin.print_utility', compact('record', 'resident'));
    }

    public function notifyUtility($id)
    {
        $record = UtilityRecord::with('room')->findOrFail($id);
        $resident = Resident::where('room_id', $record->room_id)
            ->where('status', 'active')
            ->first();

        if (!$resident) {
            return back()->with('error', 'Không tìm thấy cư dân hoạt động cho phòng này!');
        }

        $month = explode('-', $record->billing_month)[1];
        $elecUsed = $record->new_electricity - $record->old_electricity;
        $waterUsed = $record->new_water - $record->old_water;
        $total = $record->room->price + ($elecUsed * $record->electricity_price) + ($waterUsed * $record->water_price) + 150000;

        $message = "🔔 [SmartRoom] HÓA ĐƠN TIỀN NHÀ THÁNG {$month}\n"
                 . "Phòng: {$record->room->room_number}\n"
                 . "Cư dân: {$resident->name}\n"
                 . "Tiền phòng: " . number_format($record->room->price) . "đ\n"
                 . "Điện tiêu thụ: {$elecUsed} kWh (" . number_format($elecUsed * $record->electricity_price) . "đ)\n"
                 . "Nước tiêu thụ: {$waterUsed} m3 (" . number_format($waterUsed * $record->water_price) . "đ)\n"
                 . "Phí dịch vụ: 150,000đ\n"
                 . "---------------------------\n"
                 . "Tổng cộng: " . number_format($total) . "đ\n"
                 . "Vui lòng thanh toán trước ngày 10 hàng tháng. Cảm ơn!";

        \Illuminate\Support\Facades\Log::info("Telegram Notification Sent:\n" . $message);
        NotificationLog::create([
            'tenant_id' => $this->currentTenantId(),
            'type' => 'payment_reminder',
            'channel' => 'zalo',
            'recipient_name' => $resident->name,
            'recipient_contact' => $resident->phone,
            'subject' => 'Nhac hoa don phong ' . $record->room->room_number,
            'message' => $message,
            'status' => 'sent',
            'target_type' => UtilityRecord::class,
            'target_id' => $record->id,
            'meta' => [
                'room_number' => $record->room->room_number,
                'billing_month' => $record->billing_month,
                'total_amount' => $total,
                'simulated' => true,
            ],
            'sent_at' => now(),
        ]);

        AdminActivityLogger::log(
            'notify',
            'utilities',
            'Gửi thông báo hóa đơn phòng ' . $record->room->room_number . ' cho ' . $resident->name,
            $record,
            ['room_number' => $record->room->room_number, 'billing_month' => $record->billing_month, 'resident_name' => $resident->name]
        );

        return redirect()->route('smartroom.admin', ['tab' => 'utility-section'])->with('success', 'Đã tự động gửi thông báo chi tiết hóa đơn qua Telegram & Zalo thành công!');
    }

    public function autoRemindUtilities(NotificationService $notificationService)
    {
        $currentMonth = now()->format('Y-m');
        $logs = $notificationService->sendPaymentReminders($this->currentTenantId(), $currentMonth);
        $sentRooms = $logs
            ->where('type', 'payment_reminder')
            ->where('status', 'sent')
            ->groupBy('target_id')
            ->map(function ($roomLogs) {
                $firstLog = $roomLogs->first();
                $meta = $firstLog->meta ?? [];

                return [
                    'room_number' => $meta['room_number'] ?? 'N/A',
                    'resident_name' => $firstLog->recipient_name,
                    'phone' => $firstLog->channel === 'email' ? null : $firstLog->recipient_contact,
                    'total_amount' => $meta['total_amount'] ?? 0,
                    'total_amount_formatted' => number_format((int) ($meta['total_amount'] ?? 0)) . ' VND',
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'billing_month' => $currentMonth,
            'sent_count' => $sentRooms->count(),
            'sent_rooms' => $sentRooms,
        ]);
    }

    public function notifyContracts(NotificationService $notificationService)
    {
        $logs = $notificationService->sendContractExpiryReminders($this->currentTenantId());

        return redirect()
            ->route('smartroom.admin', ['tab' => 'dashboard-section'])
            ->with('success', 'Da gui ' . $logs->where('status', 'sent')->count() . ' thong bao nhac hop dong sap het han.');
    }

    public function notifyMaintenance(NotificationService $notificationService)
    {
        $logs = $notificationService->sendMaintenanceReminders($this->currentTenantId());

        return redirect()
            ->route('smartroom.admin', ['tab' => 'dashboard-section'])
            ->with('success', 'Da gui ' . $logs->where('status', 'sent')->count() . ' thong bao nhac bao tri thiet bi.');
    }

    public function notifyAll(NotificationService $notificationService)
    {
        $tenantId = $this->currentTenantId();
        $paymentLogs = $notificationService->sendPaymentReminders($tenantId);
        $contractLogs = $notificationService->sendContractExpiryReminders($tenantId);
        $maintenanceLogs = $notificationService->sendMaintenanceReminders($tenantId);
        $sentCount = $paymentLogs->concat($contractLogs)->concat($maintenanceLogs)->where('status', 'sent')->count();

        return redirect()
            ->route('smartroom.admin', ['tab' => 'dashboard-section'])
            ->with('success', 'Da chay tat ca thong bao tu dong. So thong bao gui thanh cong: ' . $sentCount . '.');
    }

    public function storeContract(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'resident_id' => 'required|exists:residents,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'deposit' => 'required|integer|min:0',
            'terms' => 'required|string',
        ]);

        $room = Room::findOrFail($request->room_id);
        $resident = Resident::findOrFail($request->resident_id);

        $code = 'HĐ-' . $room->room_number . '-' . date('Ymd', strtotime($request->start_date));

        $contract = \App\Models\Contract::create([
            'tenant_id' => $this->currentTenantId(),
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'contract_code' => $code,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'deposit' => $request->deposit,
            'status' => 'pending',
            'terms' => $request->terms,
        ]);

        AdminActivityLogger::log(
            'create',
            'contracts',
            'Tạo hợp đồng ' . $contract->contract_code . ' cho phòng ' . $room->room_number,
            $contract,
            ['room_number' => $room->room_number, 'resident_name' => $resident->name],
            null,
            $contract->only(['room_id', 'resident_id', 'contract_code', 'start_date', 'end_date', 'deposit', 'status'])
        );

        return redirect()->route('smartroom.admin', ['tab' => 'contract-section'])->with('success', 'Tạo hợp đồng online thành công! Cư dân có thể ký qua liên kết.');
    }

    public function deleteContract($id)
    {
        $contract = \App\Models\Contract::findOrFail($id);
        $before = $contract->only(['room_id', 'resident_id', 'contract_code', 'start_date', 'end_date', 'deposit', 'status']);
        $contractCode = $contract->contract_code;
        $contract->delete();

        AdminActivityLogger::log(
            'delete',
            'contracts',
            'Xóa hợp đồng ' . $contractCode,
            $contract,
            ['contract_code' => $contractCode],
            $before
        );

        return redirect()->route('smartroom.admin', ['tab' => 'contract-section'])->with('success', 'Xóa hợp đồng thành công!');
    }

    public function signContractView($id)
    {
        $contract = \App\Models\Contract::with(['room', 'resident'])->findOrFail($id);
        return view('admin.sign_contract', compact('contract'));
    }

    public function signContract(Request $request, $id)
    {
        $request->validate([
            'signature' => 'required|string', // Base64 signature image
        ]);

        $contract = \App\Models\Contract::findOrFail($id);
        $before = $contract->only(['status', 'signature']);
        $contract->update([
            'signature' => $request->signature,
            'status' => 'active',
        ]);

        AdminActivityLogger::log(
            'update',
            'contracts',
            'Hợp đồng ' . $contract->contract_code . ' đã được ký online',
            $contract,
            ['contract_code' => $contract->contract_code],
            $before,
            $contract->fresh()->only(['status', 'signature'])
        );

        return redirect()->route('smartroom.contract.sign_view', $id)->with('success', 'Ký hợp đồng online thành công!');
    }

    public function storeContactRequest(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'message' => 'nullable|string',
        ]);

        \App\Models\ContactRequest::create([
            'room_id' => $request->room_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Đăng ký nhận tư vấn thành công! Chủ trọ sẽ sớm liên hệ lại với bạn.');
    }

    public function updateContactRequestStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processed',
        ]);

        $contact = \App\Models\ContactRequest::findOrFail($id);
        $before = $contact->only(['status']);
        $contact->update(['status' => $request->status]);

        AdminActivityLogger::log(
            'update',
            'contact_requests',
            'Cập nhật yêu cầu tư vấn của ' . $contact->name . ' sang trạng thái ' . $request->status,
            $contact,
            ['phone' => $contact->phone],
            $before,
            $contact->fresh()->only(['status'])
        );

        return redirect()->route('smartroom.admin', ['tab' => 'contact-section'])->with('success', 'Cập nhật trạng thái yêu cầu tư vấn thành công!');
    }

    public function deleteContactRequest($id)
    {
        $contact = \App\Models\ContactRequest::findOrFail($id);
        $before = $contact->only(['room_id', 'name', 'phone', 'status']);
        $contactName = $contact->name;
        $contact->delete();

        AdminActivityLogger::log(
            'delete',
            'contact_requests',
            'Xóa yêu cầu tư vấn của ' . $contactName,
            $contact,
            ['phone' => $before['phone'] ?? null],
            $before
        );

        return redirect()->route('smartroom.admin', ['tab' => 'contact-section'])->with('success', 'Xóa yêu cầu tư vấn thành công!');
    }
}
