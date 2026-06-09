<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Building;
use App\Models\Room;
use App\Models\Resident;
use App\Models\Contract;
use App\Models\ElectricWaterLog;
use App\Models\Bill;
use App\Models\Ticket;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class TenantAdminController extends Controller
{
    // 1. Dashboard tổng quan
    public function dashboard(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        // Stats ribbon
        $totalRooms = Room::where('tenant_id', $tenantId)->count();
        $occupiedRooms = Room::where('tenant_id', $tenantId)->where('status', 'occupied')->count();
        $emptyRooms = Room::where('tenant_id', $tenantId)->where('status', 'empty')->count();
        $overdueRooms = Room::where('tenant_id', $tenantId)->where('status', 'overdue')->count();

        // Biểu đồ doanh thu
        $monthlyRevenue = Bill::selectRaw("
            billing_month,
            SUM(total_amount) as total_revenue
        ")
        ->where('tenant_id', $tenantId)
        ->where('status', 'paid')
        ->groupBy('billing_month')
        ->orderBy('billing_month')
        ->get();

        $chartMonths = [];
        $chartRevenue = [];
        foreach ($monthlyRevenue as $rev) {
            $monthNum = explode('-', $rev->billing_month)[1];
            $chartMonths[] = 'Tháng ' . intval($monthNum);
            $chartRevenue[] = (int) $rev->total_revenue;
        }

        // Hoạt động gần đây (lấy từ các hóa đơn mới nhất và sự cố mới nhất)
        $recentBills = Bill::with('room')->where('tenant_id', $tenantId)->latest()->take(3)->get();
        $recentTickets = Ticket::with('room')->where('tenant_id', $tenantId)->latest()->take(3)->get();

        $activities = [];
        foreach ($recentBills as $b) {
            $activities[] = [
                'time' => $b->created_at->diffForHumans(),
                'icon' => 'fa-file-invoice-dollar text-primary',
                'desc' => "Đã tạo hóa đơn phòng {$b->room->room_number} tháng " . explode('-', $b->billing_month)[1]
            ];
        }
        foreach ($recentTickets as $t) {
            $activities[] = [
                'time' => $t->created_at->diffForHumans(),
                'icon' => 'fa-wrench text-warning',
                'desc' => "Cư dân phòng {$t->room->room_number} báo sự cố: {$t->title}"
            ];
        }

        return response()->json([
            'success' => true,
            'stats' => [
                'total_rooms' => $totalRooms,
                'occupied_rooms' => $occupiedRooms,
                'empty_rooms' => $emptyRooms,
                'overdue_rooms' => $overdueRooms,
            ],
            'revenue_chart' => [
                'labels' => $chartMonths,
                'data' => $chartRevenue,
            ],
            'recent_activities' => $activities
        ]);
    }

    // 2. Quản lý Tòa nhà (Buildings)
    public function listBuildings()
    {
        $tenantId = Auth::user()->tenant_id;
        $buildings = Building::where('tenant_id', $tenantId)->get();
        return response()->json(['success' => true, 'buildings' => $buildings]);
    }

    public function storeBuilding(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $tenantId = Auth::user()->tenant_id;

        $building = Building::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description
        ]);

        return response()->json(['success' => true, 'message' => 'Tạo tòa nhà thành công', 'building' => $building], 201);
    }

    public function updateBuilding(Request $request, $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $building = Building::where('tenant_id', $tenantId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $building->update($request->only('name', 'address', 'description'));

        return response()->json(['success' => true, 'message' => 'Cập nhật tòa nhà thành công', 'building' => $building]);
    }

    public function deleteBuilding($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $building = Building::where('tenant_id', $tenantId)->findOrFail($id);
        $building->delete();

        return response()->json(['success' => true, 'message' => 'Xóa tòa nhà thành công']);
    }

    // 3. Quản lý Phòng trọ (Rooms)
    public function listRooms(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $query = Room::with('building')->where('tenant_id', $tenantId);

        if ($request->has('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        $rooms = $query->orderBy('room_number')->get();
        return response()->json(['success' => true, 'rooms' => $rooms]);
    }

    public function storeRoom(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'room_number' => 'required|string|max:50',
            'floor' => 'required|integer',
            'price' => 'required|integer|min:0',
            'area' => 'required|integer|min:0',
            'amenities' => 'nullable|array',
            'description' => 'nullable|string'
        ]);

        // Kiểm tra building có thuộc về tenant không
        Building::where('tenant_id', $tenantId)->findOrFail($request->building_id);

        // Kiểm tra duy nhất số phòng trong tòa nhà
        $exists = Room::where('building_id', $request->building_id)
            ->where('room_number', $request->room_number)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Số phòng này đã tồn tại trong tòa nhà'], 412);
        }

        $room = Room::create([
            'building_id' => $request->building_id,
            'tenant_id' => $tenantId,
            'room_number' => $request->room_number,
            'floor' => $request->floor,
            'status' => 'empty',
            'price' => $request->price,
            'area' => $request->area,
            'amenities' => $request->amenities ?? [],
            'description' => $request->description
        ]);

        return response()->json(['success' => true, 'message' => 'Thêm phòng thành công', 'room' => $room], 201);
    }

    public function updateRoom(Request $request, $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $room = Room::where('tenant_id', $tenantId)->findOrFail($id);

        $request->validate([
            'room_number' => 'required|string|max:50',
            'floor' => 'required|integer',
            'price' => 'required|integer|min:0',
            'area' => 'required|integer|min:0',
            'amenities' => 'nullable|array',
            'status' => 'required|in:empty,occupied,overdue,maintenance',
            'description' => 'nullable|string'
        ]);

        // Kiểm tra tính duy nhất số phòng nếu đổi số phòng
        if ($request->room_number !== $room->room_number) {
            $exists = Room::where('building_id', $room->building_id)
                ->where('room_number', $request->room_number)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Số phòng này đã tồn tại trong tòa nhà'], 412);
            }
        }

        $room->update($request->only('room_number', 'floor', 'price', 'area', 'amenities', 'status', 'description'));

        return response()->json(['success' => true, 'message' => 'Cập nhật phòng thành công', 'room' => $room]);
    }

    public function deleteRoom($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $room = Room::where('tenant_id', $tenantId)->findOrFail($id);
        $room->delete();

        return response()->json(['success' => true, 'message' => 'Xóa phòng thành công']);
    }

    // 4. Sơ đồ phòng trực quan (Room Matrix)
    public function roomMatrix(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $buildingId = $request->input('building_id');

        if (!$buildingId) {
            // Lấy building đầu tiên của tenant nếu không truyền
            $firstBuilding = Building::where('tenant_id', $tenantId)->first();
            if (!$firstBuilding) {
                return response()->json(['success' => true, 'floors' => []]);
            }
            $buildingId = $firstBuilding->id;
        }

        // Kiểm tra xem building thuộc về tenant không
        Building::where('tenant_id', $tenantId)->findOrFail($buildingId);

        $rooms = Room::with(['residents' => function($q) {
            $q->where('status', 'active');
        }, 'bills' => function($q) {
            $q->orderBy('billing_month', 'desc');
        }])
        ->where('building_id', $buildingId)
        ->orderBy('floor')
        ->orderBy('room_number')
        ->get();

        $roomsByFloor = $rooms->groupBy('floor');

        $floorsData = [];
        foreach ($roomsByFloor as $floor => $floorRooms) {
            $roomsList = $floorRooms->map(function ($r) {
                $activeResident = $r->residents->first();
                $latestBill = $r->bills->first();

                return [
                    'id' => $r->id,
                    'room_number' => $r->room_number,
                    'floor' => $r->floor,
                    'status' => $r->status, // empty - Xanh, occupied - Đỏ, overdue - Vàng
                    'price' => $r->price,
                    'area' => $r->area,
                    'amenities' => $r->amenities,
                    'resident' => $activeResident ? [
                        'id' => $activeResident->id,
                        'name' => $activeResident->name,
                        'phone' => $activeResident->phone,
                    ] : null,
                    'latest_bill' => $latestBill ? [
                        'id' => $latestBill->id,
                        'month' => $latestBill->billing_month,
                        'total_amount' => $latestBill->total_amount,
                        'status' => $latestBill->status,
                        'vietqr_url' => $latestBill->vietqr_url,
                    ] : null,
                ];
            });

            $floorsData[] = [
                'floor' => $floor,
                'rooms' => $roomsList
            ];
        }

        return response()->json([
            'success' => true,
            'building_id' => $buildingId,
            'floors' => $floorsData
        ]);
    }

    // 5. Quản lý cư dân (Resident CRUD)
    public function listResidents()
    {
        $tenantId = Auth::user()->tenant_id;
        $residents = Resident::with('room.building')->where('tenant_id', $tenantId)->orderBy('id', 'desc')->get();
        return response()->json(['success' => true, 'residents' => $residents]);
    }

    public function storeResident(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'cccd' => 'required|string|max:20',
            'start_date' => 'required|date'
        ]);

        // Kiểm tra room có thuộc về tenant không
        $room = Room::where('tenant_id', $tenantId)->findOrFail($request->room_id);

        if ($room->status !== 'empty') {
            return response()->json(['success' => false, 'message' => 'Phòng này hiện đang có người ở'], 412);
        }

        if (!$room->canAcceptOccupants(1)) {
            return response()->json([
                'success' => false,
                'message' => 'Phòng này đã đạt giới hạn tối đa ' . Room::MAX_OCCUPANTS . ' người ở'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Tạo tài khoản User cho cư dân để họ có thể đăng nhập dashboard di động
            // Kiểm tra email user tồn tại chưa
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                $roleResident = Role::where('slug', 'resident')->first();
                $user = User::create([
                    'tenant_id' => $tenantId,
                    'role_id' => $roleResident->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make('123456') // Mật khẩu mặc định
                ]);
            }

            // 2. Tạo cư dân
            $resident = Resident::create([
                'tenant_id' => $tenantId,
                'room_id' => $room->id,
                'user_id' => $user->id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'cccd' => $request->cccd,
                'start_date' => $request->start_date,
                'status' => 'active'
            ]);

            // 3. Cập nhật trạng thái phòng thành occupied
            $room->update(['status' => 'occupied']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thêm cư dân mới và kích hoạt tài khoản thành công',
                'resident' => $resident
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    public function updateResident(Request $request, $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $resident = Resident::where('tenant_id', $tenantId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'cccd' => 'required|string|max:20',
            'start_date' => 'required|date',
            'status' => 'required|in:active,inactive'
        ]);

        DB::beginTransaction();
        try {
            $resident->update($request->only('name', 'phone', 'email', 'cccd', 'start_date', 'status'));

            // Đồng bộ tên và số điện thoại sang tài khoản user liên kết
            if ($resident->user) {
                $resident->user->update([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email
                ]);
            }

            // Nếu cư dân chuyển sang inactive, trả trạng thái phòng về empty
            if ($request->status === 'inactive' && $resident->room_id) {
                $room = Room::findOrFail($resident->room_id);
                $resident->room_id = null;
                $resident->save();

                // Kiểm tra xem phòng còn cư dân active nào khác không
                $room->syncOccupancyStatus();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cập nhật cư dân thành công', 'resident' => $resident]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    public function deleteResident($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $resident = Resident::where('tenant_id', $tenantId)->findOrFail($id);
        $room = $resident->room;

        DB::beginTransaction();
        try {
            // Xóa tài khoản User liên kết nếu cần thiết (ở đây xóa resident và gỡ room)
            $resident->delete();

            if ($room) {
                $room->syncOccupancyStatus();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Đã xóa cư dân và cập nhật trạng thái phòng thành công']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    // 6. Quản lý hợp đồng
    public function listContracts()
    {
        $tenantId = Auth::user()->tenant_id;
        $contracts = Contract::with(['room', 'resident'])->where('tenant_id', $tenantId)->orderBy('id', 'desc')->get();
        return response()->json(['success' => true, 'contracts' => $contracts]);
    }

    public function storeContract(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'resident_id' => 'required|exists:residents,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'deposit' => 'required|integer|min:0',
            'terms' => 'required|string'
        ]);

        $room = Room::where('tenant_id', $tenantId)->findOrFail($request->room_id);
        $resident = Resident::where('tenant_id', $tenantId)->findOrFail($request->resident_id);

        $code = 'HĐ-' . $room->room_number . '-' . date('Ymd', strtotime($request->start_date));

        $contract = Contract::create([
            'tenant_id' => $tenantId,
            'room_id' => $room->id,
            'resident_id' => $resident->id,
            'contract_code' => $code,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'deposit' => $request->deposit,
            'status' => 'pending',
            'terms' => $request->terms
        ]);

        return response()->json(['success' => true, 'message' => 'Tạo hợp đồng online thành công', 'contract' => $contract], 201);
    }

    public function deleteContract($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $contract = Contract::where('tenant_id', $tenantId)->findOrFail($id);
        $contract->delete();

        return response()->json(['success' => true, 'message' => 'Xóa hợp đồng thành công']);
    }

    // 7. Chốt điện nước & tính tiền tự động
    public function storeUtilityRecord(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $tenant = Tenant::findOrFail($tenantId);

        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'billing_month' => 'required|string', // Format "YYYY-MM"
            'new_electricity' => 'required|integer',
            'new_water' => 'required|integer',
            'electricity_price' => 'nullable|integer|min:0',
            'water_price' => 'nullable|integer|min:0',
            'service_cost' => 'nullable|integer|min:0',
        ]);

        $room = Room::where('tenant_id', $tenantId)->findOrFail($request->room_id);

        // Lấy chỉ số mới nhất trước đó làm chỉ số cũ
        $latestRecord = ElectricWaterLog::where('room_id', $room->id)
            ->orderBy('billing_month', 'desc')
            ->first();

        $oldElec = $latestRecord ? $latestRecord->new_electricity : 0;
        $oldWater = $latestRecord ? $latestRecord->new_water : 0;

        if ($request->new_electricity < $oldElec || $request->new_water < $oldWater) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ số mới không được nhỏ hơn chỉ số cũ! (Điện cũ: ' . $oldElec . ', Nước cũ: ' . $oldWater . ')'
            ], 412);
        }

        $elecPrice = $request->input('electricity_price', 3500);
        $waterPrice = $request->input('water_price', 15000);
        $serviceCost = $request->input('service_cost', 150000);

        DB::beginTransaction();
        try {
            // 1. Lưu chỉ số điện nước
            $log = ElectricWaterLog::create([
                'tenant_id' => $tenantId,
                'room_id' => $room->id,
                'billing_month' => $request->billing_month,
                'old_electricity' => $oldElec,
                'new_electricity' => $request->new_electricity,
                'old_water' => $oldWater,
                'new_water' => $request->new_water,
                'electricity_price' => $elecPrice,
                'water_price' => $waterPrice
            ]);

            // 2. Tính tiền hóa đơn tự động
            $elecUsed = $request->new_electricity - $oldElec;
            $waterUsed = $request->new_water - $oldWater;

            $elecCost = $elecUsed * $elecPrice;
            $waterCost = $waterUsed * $waterPrice;
            $totalAmount = $room->price + $elecCost + $waterCost + $serviceCost;

            // 3. Sinh VietQR động thanh toán theo chuẩn Napas247
            $bankId = $tenant->bank_name;
            $accountNo = $tenant->bank_account_no;
            $accountName = $tenant->bank_account_name;
            $addInfo = "Thanh toan Phong {$room->room_number} thang " . explode('-', $request->billing_month)[1];
            $vietqrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$totalAmount}&addInfo=" . rawurlencode($addInfo) . "&accountName=" . rawurlencode($accountName);

            // 4. Tạo bản ghi hóa đơn
            $bill = Bill::create([
                'tenant_id' => $tenantId,
                'room_id' => $room->id,
                'electric_water_log_id' => $log->id,
                'billing_month' => $request->billing_month,
                'room_price' => $room->price,
                'electricity_usage' => $elecUsed,
                'electricity_cost' => $elecCost,
                'water_usage' => $waterUsed,
                'water_cost' => $waterCost,
                'service_cost' => $serviceCost,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'vietqr_url' => $vietqrUrl
            ]);

            // 5. Đánh dấu trạng thái phòng là quá hạn / nợ phí
            $room->update(['status' => 'overdue']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã chốt điện nước và tạo hóa đơn thành công',
                'bill' => $bill
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    // 8. Xuất hóa đơn PDF
    public function printPdf($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $bill = Bill::with(['room.building', 'electricWaterLog'])->where('tenant_id', $tenantId)->findOrFail($id);
        $resident = Resident::where('room_id', $bill->room_id)->where('status', 'active')->first();

        $data = [
            'bill' => $bill,
            'tenant' => Auth::user()->tenant,
            'resident' => $resident
        ];

        // Xuất PDF sử dụng DomPDF
        $pdf = Pdf::loadView('admin.pdf_bill', $data);

        return $pdf->download('hoa_don_phong_' . $bill->room->room_number . '_' . $bill->billing_month . '.pdf');
    }

    // 9. Gửi thông báo tự động (Email + Telegram Bot)
    public function notifyResident($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $bill = Bill::with('room')->where('tenant_id', $tenantId)->findOrFail($id);
        $resident = Resident::where('room_id', $bill->room_id)->where('status', 'active')->first();

        if (!$resident) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy cư dân hoạt động trong phòng'], 404);
        }

        $month = explode('-', $bill->billing_month)[1];

        // Bản thông tin chi tiết
        $message = "🔔 [SmartRoom] HÓA ĐƠN TIỀN NHÀ THÁNG {$month}\n"
                 . "Phòng: {$bill->room->room_number}\n"
                 . "Cư dân: {$resident->name}\n"
                 . "Tiền phòng: " . number_format($bill->room_price) . "đ\n"
                 . "Điện tiêu thụ: {$bill->electricity_usage} kWh (" . number_format($bill->electricity_cost) . "đ)\n"
                 . "Nước tiêu thụ: {$bill->water_usage} m3 (" . number_format($bill->water_cost) . "đ)\n"
                 . "Phí dịch vụ: " . number_format($bill->service_cost) . "đ\n"
                 . "---------------------------\n"
                 . "Tổng cộng: " . number_format($bill->total_amount) . "đ\n"
                 . "Link thanh toán QR: {$bill->vietqr_url}\n"
                 . "Vui lòng thanh toán đúng hạn. Cảm ơn!";

        // Gửi qua Mail (Giả lập ghi log hoặc cấu hình mail)
        try {
            if ($resident->email) {
                // Laravel Mail dispatch
                \Illuminate\Support\Facades\Mail::raw($message, function ($m) use ($resident, $month) {
                    $m->to($resident->email)->subject("Hóa đơn tiền nhà Tháng " . $month);
                });
            }
        } catch (\Exception $e) {
            Log::error("Mail sending failed: " . $e->getMessage());
        }

        // Gửi qua Telegram Bot (Đọc token cấu hình từ env)
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if ($botToken && $chatId) {
            try {
                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message
                ]);
            } catch (\Exception $e) {
                Log::error("Telegram Notification failed: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Thông báo đã được gửi thành công qua Email & Telegram Bot',
            'content' => $message
        ]);
    }

    // 10. Quản lý sự cố (Ticket Center)
    public function listTickets()
    {
        $tenantId = Auth::user()->tenant_id;
        $tickets = Ticket::with(['room', 'resident'])->where('tenant_id', $tenantId)->orderBy('id', 'desc')->get();
        return response()->json(['success' => true, 'tickets' => $tickets]);
    }

    public function updateTicket(Request $request, $id)
    {
        $tenantId = Auth::user()->tenant_id;
        $ticket = Ticket::where('tenant_id', $tenantId)->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,resolved',
            'assigned_to' => 'nullable|string|max:255'
        ]);

        $ticket->update([
            'status' => $request->status,
            'assigned_to' => $request->assigned_to
        ]);

        // Gửi thông báo tự động khi ticket được xử lý / phân công
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        if ($botToken && $chatId) {
            $msg = "🛠️ [SmartRoom] Cập nhật tiến độ sự cố #{$ticket->id}\n"
                 . "Phòng: {$ticket->room->room_number}\n"
                 . "Sự cố: {$ticket->title}\n"
                 . "Trạng thái mới: " . ($request->status === 'resolved' ? 'Đã sửa xong' : ($request->status === 'processing' ? 'Đang xử lý' : 'Chờ duyệt')) . "\n"
                 . "Kỹ thuật phụ trách: " . ($request->assigned_to ?: 'Chưa phân công');
            try {
                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $msg
                ]);
            } catch (\Exception $e) {
                Log::error("Telegram Ticket update failed: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái sự cố thành công',
            'ticket' => $ticket
        ]);
    }
}
