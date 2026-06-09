<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Role;
use App\Models\User;
use App\Models\Building;
use App\Models\Room;
use App\Models\Resident;
use App\Models\ElectricWaterLog;
use App\Models\Bill;
use App\Models\Ticket;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SmartRoomSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo các vai trò (Roles)
        $roleLandlord = Role::updateOrCreate([
            'slug' => 'landlord',
        ], [
            'name' => 'Chủ trọ / Quản lý',
            'description' => 'Quản lý phòng trọ, hóa đơn, cư dân và xử lý sự cố.'
        ]);

        $roleResident = Role::updateOrCreate([
            'slug' => 'resident',
        ], [
            'name' => 'Cư dân thuê phòng',
            'description' => 'Khách thuê phòng nội bộ, xem hóa đơn, gửi yêu cầu báo hỏng.'
        ]);

        $roleGuest = Role::updateOrCreate([
            'slug' => 'guest',
        ], [
            'name' => 'Người tìm trọ',
            'description' => 'Khách vãng lai tìm kiếm phòng trọ, viết đánh giá.'
        ]);

        // 2. Tạo 2 Tenants (2 Chủ trọ khác nhau để demo Multi-tenant)
        $tenant1 = Tenant::updateOrCreate([
            'email' => 'contact@smartroom-caugiay.vn',
        ], [
            'name' => 'Hệ thống SmartRoom Cầu Giấy',
            'phone' => '0988123456',
            'bank_name' => 'MB',
            'bank_account_no' => '9999888889999',
            'bank_account_name' => 'NGUYEN VAN CHU NHA'
        ]);

        $tenant2 = Tenant::whereIn('email', [
            'contact@rentry-thanhxuan.vn',
            'contact@renty-thanhxuan.vn',
        ])->first() ?? new Tenant();
        $tenant2->fill([
            'name' => 'Hệ thống Rentry Home Thanh Xuân',
            'email' => 'contact@rentry-thanhxuan.vn',
            'phone' => '0977222333',
            'bank_name' => 'VCB',
            'bank_account_no' => '1234567890',
            'bank_account_name' => 'LE ANH QUAN LY'
        ])->save();

        // 3. Tạo tài khoản Tenant Admins (Chủ trọ)
        $landlord1 = User::updateOrCreate([
            'username' => 'landlord1',
        ], [
            'tenant_id' => $tenant1->id,
            'role_id' => $roleLandlord->id,
            'name' => 'Nguyễn Văn Chủ Nhà',
            'email' => 'landlord1@gmail.com',
            'phone' => '0988123456',
            'password' => Hash::make('password')
        ]);

        $landlord2 = User::updateOrCreate([
            'username' => 'landlord2',
        ], [
            'tenant_id' => $tenant2->id,
            'role_id' => $roleLandlord->id,
            'name' => 'Lê Anh Quản Lý',
            'email' => 'landlord2@gmail.com',
            'phone' => '0977222333',
            'password' => Hash::make('password')
        ]);

        // Tạo tài khoản Demo Guest
        User::updateOrCreate([
            'username' => 'guest',
        ], [
            'tenant_id' => null,
            'role_id' => $roleGuest->id,
            'name' => 'Nguyễn Tìm Phòng',
            'email' => 'guest@gmail.com',
            'phone' => '0900000001',
            'password' => Hash::make('password')
        ]);

        // 4. Tạo các Tòa nhà (Buildings)
        $building1 = Building::updateOrCreate([
            'tenant_id' => $tenant1->id,
            'name' => 'SmartRoom Cầu Giấy',
        ], [
            'address' => 'Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy, Hà Nội',
            'description' => 'Chung cư mini cao cấp, gần ĐH Sư Phạm, ĐH Quốc Gia. Đầy đủ khóa vân tay, camera an ninh, gác lửng.'
        ]);

        $building2 = Building::where('tenant_id', $tenant2->id)
            ->whereIn('name', ['Rentry Home Thanh Xuân', 'Renty Home Thanh Xuân'])
            ->first() ?? new Building(['tenant_id' => $tenant2->id]);
        $building2->fill([
            'tenant_id' => $tenant2->id,
            'name' => 'Rentry Home Thanh Xuân',
            'address' => 'Số 85 Vũ Tông Phan, Thanh Xuân, Hà Nội',
            'description' => 'Tòa nhà mới xây, phòng studio có ban công rộng, đầy đủ đồ cơ bản.'
        ])->save();

        // 5. Tạo các Phòng trọ (Rooms) cho Building 1 (Cầu Giấy - Tenant 1)
        $roomDataT1 = [
            // Floor 1
            ['room_number' => '101', 'floor' => 1, 'status' => 'occupied', 'price' => 3200000, 'area' => 25, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng', 'tủ quần áo', 'tự do']],
            ['room_number' => '102', 'floor' => 1, 'status' => 'occupied', 'price' => 3200000, 'area' => 25, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng', 'tủ quần áo']],
            ['room_number' => '103', 'floor' => 1, 'status' => 'overdue',  'price' => 3500000, 'area' => 28, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng', 'máy giặt riêng', 'cho nuôi thú cưng']],
            ['room_number' => '104', 'floor' => 1, 'status' => 'empty',    'price' => 3500000, 'area' => 28, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng', 'ban công']],

            // Floor 2
            ['room_number' => '201', 'floor' => 2, 'status' => 'occupied', 'price' => 3300000, 'area' => 25, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng', 'tự do']],
            ['room_number' => '202', 'floor' => 2, 'status' => 'occupied', 'price' => 3300000, 'area' => 25, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng']],
            ['room_number' => '203', 'floor' => 2, 'status' => 'overdue',  'price' => 3600000, 'area' => 28, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng', 'ban công', 'cho nuôi thú cưng']],
            ['room_number' => '204', 'floor' => 2, 'status' => 'empty',    'price' => 3600000, 'area' => 28, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng', 'ban công']],

            // Floor 3
            ['room_number' => '301', 'floor' => 3, 'status' => 'occupied', 'price' => 3500000, 'area' => 27, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng', 'ban công', 'tự do']],
            ['room_number' => '302', 'floor' => 3, 'status' => 'occupied', 'price' => 3500000, 'area' => 27, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng', 'ban công']],
            ['room_number' => '303', 'floor' => 3, 'status' => 'overdue',  'price' => 3800000, 'area' => 30, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng', 'ban công', 'máy giặt riêng', 'cho nuôi thú cưng']],
            ['room_number' => '304', 'floor' => 3, 'status' => 'empty',    'price' => 3800000, 'area' => 30, 'amenities' => ['gác lửng', 'điều hòa', 'nước nóng', 'ban công', 'tủ lạnh']],
        ];

        $roomsT1 = [];
        foreach ($roomDataT1 as $data) {
            $data['building_id'] = $building1->id;
            $data['tenant_id'] = $tenant1->id;
            $data = array_merge($data, $this->mediaForRoom($building1->name, $data['room_number']));
            $roomsT1[$data['room_number']] = Room::updateOrCreate(
                [
                    'building_id' => $building1->id,
                    'room_number' => $data['room_number'],
                ],
                $data
            );
        }

        // Tạo các Phòng trọ cho Building 2 (Thanh Xuân - Tenant 2)
        $roomDataT2 = [
            ['room_number' => '101', 'floor' => 1, 'status' => 'occupied', 'price' => 4000000, 'area' => 30, 'amenities' => ['điều hòa', 'tủ lạnh', 'máy giặt riêng', 'nước nóng']],
            ['room_number' => '102', 'floor' => 1, 'status' => 'empty',    'price' => 4000000, 'area' => 30, 'amenities' => ['điều hòa', 'tủ lạnh', 'nước nóng']],
            ['room_number' => '201', 'floor' => 2, 'status' => 'occupied', 'price' => 4200000, 'area' => 32, 'amenities' => ['điều hòa', 'tủ lạnh', 'máy giặt riêng', 'nước nóng', 'ban công']],
            ['room_number' => '202', 'floor' => 2, 'status' => 'empty',    'price' => 4200000, 'area' => 32, 'amenities' => ['điều hòa', 'tủ lạnh', 'nước nóng', 'ban công']],
        ];

        $roomsT2 = [];
        foreach ($roomDataT2 as $data) {
            $data['building_id'] = $building2->id;
            $data['tenant_id'] = $tenant2->id;
            $data = array_merge($data, $this->mediaForRoom($building2->name, $data['room_number']));
            $roomsT2[$data['room_number']] = Room::updateOrCreate(
                [
                    'building_id' => $building2->id,
                    'room_number' => $data['room_number'],
                ],
                $data
            );
        }

        // 6. Tạo Cư dân (Residents) & liên kết User Account cho Cư dân để đăng nhập
        // Cư dân Tenant 1
        $resDataT1 = [
            '101' => ['name' => 'Trần Thanh Hùng', 'phone' => '0912345678', 'email' => 'hung.tran@gmail.com', 'cccd' => '001096001234', 'start_date' => '2026-01-15'],
            '102' => ['name' => 'Nguyễn Thị Lan',  'phone' => '0987654322', 'email' => 'lan.nguyen@gmail.com',  'cccd' => '001096005678', 'start_date' => '2026-02-10'],
            '103' => ['name' => 'Lê Hoàng Nam',    'phone' => '0905123456', 'email' => 'nam.le@gmail.com',      'cccd' => '001096009999', 'start_date' => '2026-01-05'],
            '201' => ['name' => 'Phạm Minh Tuấn',  'phone' => '0933999888', 'email' => 'tuan.pham@gmail.com',  'cccd' => '002096001111', 'start_date' => '2026-03-01'],
            '202' => ['name' => 'Vũ Thu Trang',    'phone' => '0944888777', 'email' => 'trang.vu@gmail.com',    'cccd' => '002096002222', 'start_date' => '2026-03-15'],
            '203' => ['name' => 'Đặng Anh Đức',    'phone' => '0911777666', 'email' => 'duc.dang@gmail.com',    'cccd' => '002096003333', 'start_date' => '2026-02-20'],
            '301' => ['name' => 'Hoàng Quốc Việt', 'phone' => '0977666555', 'email' => 'viet.hoang@gmail.com',  'cccd' => '003096004444', 'start_date' => '2026-04-01'],
            '302' => ['name' => 'Bùi Phương Thảo', 'phone' => '0966555444', 'email' => 'thao.bui@gmail.com',    'cccd' => '003096005555', 'start_date' => '2026-04-10'],
            '303' => ['name' => 'Ngô Tiến Đạt',    'phone' => '0955444333', 'email' => 'dat.ngo@gmail.com',     'cccd' => '003096006666', 'start_date' => '2026-03-25'],
        ];

        foreach ($resDataT1 as $roomNum => $res) {
            // Tạo tài khoản User cho cư dân
            $u = User::updateOrCreate([
                'username' => 'resident-t1-' . $roomNum,
            ], [
                'tenant_id' => $tenant1->id,
                'role_id' => $roleResident->id,
                'name' => $res['name'],
                'email' => $res['email'],
                'phone' => $res['phone'],
                'password' => Hash::make('password')
            ]);

            Resident::updateOrCreate([
                'email' => $res['email'],
            ], [
                'tenant_id' => $tenant1->id,
                'room_id' => $roomsT1[$roomNum]->id,
                'user_id' => $u->id,
                'name' => $res['name'],
                'phone' => $res['phone'],
                'email' => $res['email'],
                'cccd' => $res['cccd'],
                'start_date' => $res['start_date'],
                'status' => 'active'
            ]);
        }

        // Cư dân Tenant 2
        $resDataT2 = [
            '101' => ['name' => 'Nguyễn Hoàng Sơn', 'phone' => '0912111222', 'email' => 'son.nguyen@gmail.com', 'cccd' => '004096007777', 'start_date' => '2026-04-15'],
            '201' => ['name' => 'Phạm Khánh Linh',  'phone' => '0983222333', 'email' => 'linh.pham@gmail.com',  'cccd' => '004096008888', 'start_date' => '2026-05-01'],
        ];

        foreach ($resDataT2 as $roomNum => $res) {
            // Tạo tài khoản User cho cư dân
            $u = User::updateOrCreate([
                'username' => 'resident-t2-' . $roomNum,
            ], [
                'tenant_id' => $tenant2->id,
                'role_id' => $roleResident->id,
                'name' => $res['name'],
                'email' => $res['email'],
                'phone' => $res['phone'],
                'password' => Hash::make('password')
            ]);

            Resident::updateOrCreate([
                'email' => $res['email'],
            ], [
                'tenant_id' => $tenant2->id,
                'room_id' => $roomsT2[$roomNum]->id,
                'user_id' => $u->id,
                'name' => $res['name'],
                'phone' => $res['phone'],
                'email' => $res['email'],
                'cccd' => $res['cccd'],
                'start_date' => $res['start_date'],
                'status' => 'active'
            ]);
        }

        // 7. Tạo Hóa đơn và chỉ số điện nước lịch sử (Tháng 3, 4, 5, 6 năm 2026)
        $months = ['2026-03', '2026-04', '2026-05', '2026-06'];
        
        // Chỉ số điện nước bắt đầu từ Tháng 2/2026 của Tenant 1
        $metersT1 = [
            '101' => ['elec' => 100, 'water' => 10],
            '102' => ['elec' => 150, 'water' => 15],
            '103' => ['elec' => 120, 'water' => 12],
            '201' => ['elec' => 80,  'water' => 8],
            '202' => ['elec' => 110, 'water' => 11],
            '203' => ['elec' => 95,  'water' => 9],
            '301' => ['elec' => 70,  'water' => 7],
            '302' => ['elec' => 130, 'water' => 13],
            '303' => ['elec' => 140, 'water' => 14],
        ];

        foreach ($months as $month) {
            foreach ($metersT1 as $roomNum => &$meter) {
                $room = $roomsT1[$roomNum];
                $resident = Resident::where('room_id', $room->id)->first();
                $startDate = Carbon::parse($resident->start_date);
                $monthDate = Carbon::parse($month . '-01');
                
                if ($monthDate->lt($startDate->startOfMonth())) {
                    continue;
                }

                $oldElec = $meter['elec'];
                $oldWater = $meter['water'];
                $elecUsage = rand(80, 150);
                $waterUsage = rand(4, 10);
                
                $newElec = $oldElec + $elecUsage;
                $newWater = $oldWater + $waterUsage;
                
                $meter['elec'] = $newElec;
                $meter['water'] = $newWater;

                $log = ElectricWaterLog::updateOrCreate([
                    'room_id' => $room->id,
                    'billing_month' => $month,
                ], [
                    'tenant_id' => $tenant1->id,
                    'old_electricity' => $oldElec,
                    'new_electricity' => $newElec,
                    'old_water' => $oldWater,
                    'new_water' => $newWater,
                    'electricity_price' => 3500,
                    'water_price' => 15000,
                ]);

                // Tính tiền hóa đơn
                $elecCost = $elecUsage * 3500;
                $waterCost = $waterUsage * 15000;
                $serviceCost = 150000;
                $total = $room->price + $elecCost + $waterCost + $serviceCost;

                // QR code url compact MB Bank
                $bankId = $tenant1->bank_name;
                $accountNo = $tenant1->bank_account_no;
                $accountName = $tenant1->bank_account_name;
                $addInfo = "Thanh toan Phong {$room->room_number} thang " . explode('-', $month)[1];
                $vietqrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$total}&addInfo=" . rawurlencode($addInfo) . "&accountName=" . rawurlencode($accountName);

                $status = 'paid';
                $paymentDate = Carbon::parse($month . '-10')->addDays(rand(0, 5));
                if ($month === '2026-06') {
                    if ($room->status === 'overdue') {
                        $status = 'overdue';
                        $paymentDate = null;
                    } else {
                        $status = 'pending';
                        $paymentDate = null;
                    }
                }

                Bill::updateOrCreate([
                    'room_id' => $room->id,
                    'billing_month' => $month,
                ], [
                    'tenant_id' => $tenant1->id,
                    'electric_water_log_id' => $log->id,
                    'room_price' => $room->price,
                    'electricity_usage' => $elecUsage,
                    'electricity_cost' => $elecCost,
                    'water_usage' => $waterUsage,
                    'water_cost' => $waterCost,
                    'service_cost' => $serviceCost,
                    'total_amount' => $total,
                    'status' => $status,
                    'payment_date' => $paymentDate,
                    'vietqr_url' => $vietqrUrl
                ]);
            }
        }

        // Chỉ số và hoá đơn cho Tenant 2
        $metersT2 = [
            '101' => ['elec' => 50, 'water' => 5],
            '201' => ['elec' => 60, 'water' => 6],
        ];

        foreach ($months as $month) {
            foreach ($metersT2 as $roomNum => &$meter) {
                $room = $roomsT2[$roomNum];
                $resident = Resident::where('room_id', $room->id)->first();
                $startDate = Carbon::parse($resident->start_date);
                $monthDate = Carbon::parse($month . '-01');
                
                if ($monthDate->lt($startDate->startOfMonth())) {
                    continue;
                }

                $oldElec = $meter['elec'];
                $oldWater = $meter['water'];
                $elecUsage = rand(90, 160);
                $waterUsage = rand(5, 12);
                
                $newElec = $oldElec + $elecUsage;
                $newWater = $oldWater + $waterUsage;
                
                $meter['elec'] = $newElec;
                $meter['water'] = $newWater;

                $log = ElectricWaterLog::updateOrCreate([
                    'room_id' => $room->id,
                    'billing_month' => $month,
                ], [
                    'tenant_id' => $tenant2->id,
                    'old_electricity' => $oldElec,
                    'new_electricity' => $newElec,
                    'old_water' => $oldWater,
                    'new_water' => $newWater,
                    'electricity_price' => 3800,
                    'water_price' => 18000,
                ]);

                $elecCost = $elecUsage * 3800;
                $waterCost = $waterUsage * 18000;
                $serviceCost = 150000;
                $total = $room->price + $elecCost + $waterCost + $serviceCost;

                $bankId = $tenant2->bank_name;
                $accountNo = $tenant2->bank_account_no;
                $accountName = $tenant2->bank_account_name;
                $addInfo = "Thanh toan Phong {$room->room_number} thang " . explode('-', $month)[1];
                $vietqrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$total}&addInfo=" . rawurlencode($addInfo) . "&accountName=" . rawurlencode($accountName);

                $status = 'paid';
                $paymentDate = Carbon::parse($month . '-10')->addDays(rand(0, 5));
                if ($month === '2026-06') {
                    $status = 'pending';
                    $paymentDate = null;
                }

                Bill::updateOrCreate([
                    'room_id' => $room->id,
                    'billing_month' => $month,
                ], [
                    'tenant_id' => $tenant2->id,
                    'electric_water_log_id' => $log->id,
                    'room_price' => $room->price,
                    'electricity_usage' => $elecUsage,
                    'electricity_cost' => $elecCost,
                    'water_usage' => $waterUsage,
                    'water_cost' => $waterCost,
                    'service_cost' => $serviceCost,
                    'total_amount' => $total,
                    'status' => $status,
                    'payment_date' => $paymentDate,
                    'vietqr_url' => $vietqrUrl
                ]);
            }
        }

        // 8. Tạo sự cố báo hỏng (Tickets)
        // Tenant 1 Tickets
        $res103 = Resident::where('room_id', $roomsT1['103']->id)->first();
        Ticket::updateOrCreate([
            'room_id' => $roomsT1['103']->id,
            'title' => 'Hỏng vòi sen nhà tắm',
        ], [
            'tenant_id' => $tenant1->id,
            'resident_id' => $res103->id,
            'description' => 'Vòi sen tắm bị rỉ nước liên tục làm thất thoát nước và ẩm ướt nhà tắm.',
            'category' => 'nước',
            'status' => 'pending'
        ]);

        $res203 = Resident::where('room_id', $roomsT1['203']->id)->first();
        Ticket::updateOrCreate([
            'room_id' => $roomsT1['203']->id,
            'title' => 'Điều hòa không mát',
        ], [
            'tenant_id' => $tenant1->id,
            'resident_id' => $res203->id,
            'description' => 'Điều hòa bật lên gió thổi nhẹ và không lạnh chút nào, cục nóng chạy kêu to.',
            'category' => 'điện',
            'status' => 'processing',
            'assigned_to' => 'Thợ điện lạnh Hùng'
        ]);

        // Tenant 2 Tickets
        $res101T2 = Resident::where('room_id', $roomsT2['101']->id)->first();
        Ticket::updateOrCreate([
            'room_id' => $roomsT2['101']->id,
            'title' => 'Kẹt khóa cửa phòng',
        ], [
            'tenant_id' => $tenant2->id,
            'resident_id' => $res101T2->id,
            'description' => 'Khóa cơ phòng 101 bị rít, rất khó cắm chìa khóa để vặn mở cửa.',
            'category' => 'nội thất',
            'status' => 'resolved',
            'assigned_to' => 'Thợ sửa khóa Minh'
        ]);

        // 10. Tạo các đánh giá (Reviews)
        $reviewsData = [
            '101' => [
                ['author_name' => 'Nguyễn Minh Anh', 'rating' => 5, 'comment' => 'Phòng sạch sẽ thoáng mát, chủ nhà rất thân thiện và nhiệt tình hỗ trợ khi có sự cố.'],
                ['author_name' => 'Lê Thanh Bình', 'rating' => 4, 'comment' => 'An ninh tốt, khóa vân tay an toàn. Giá điện nước hơi cao một chút nhưng dịch vụ ổn.']
            ],
            '102' => [
                ['author_name' => 'Phạm Lan Anh', 'rating' => 4, 'comment' => 'Phòng đầy đủ tiện nghi, có ban công phơi đồ rất tiện lợi. Khu vực yên tĩnh.']
            ],
            '103' => [
                ['author_name' => 'Trần Văn Cường', 'rating' => 3, 'comment' => 'Phòng hơi chật so với ảnh, nhưng bù lại an ninh tốt và gần trạm xe buýt.']
            ],
            '201' => [
                ['author_name' => 'Ngô Quốc Khánh', 'rating' => 5, 'comment' => 'Mọi thứ đều tuyệt vời! Phòng có gác lửng thiết kế đẹp, tiện ích đầy đủ.']
            ],
            '202' => [
                ['author_name' => 'Đỗ Phương Thảo', 'rating' => 5, 'comment' => 'Chủ trọ hỗ trợ nhanh chóng. Giờ giấc tự do, không chung chủ. Rất hài lòng!']
            ]
        ];

        foreach ($reviewsData as $roomNum => $reviews) {
            if (isset($roomsT1[$roomNum])) {
                foreach ($reviews as $rev) {
                    Review::updateOrCreate([
                        'room_id' => $roomsT1[$roomNum]->id,
                        'author_name' => $rev['author_name'],
                        'comment' => $rev['comment'],
                    ], [
                        'rating' => $rev['rating'],
                    ]);
                }
            }
        }
    }

    private function mediaForRoom(string $buildingName, string $roomNumber): array
    {
        $sets = [
            [
                'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1588153203274-4a392d28ed9f?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1400&q=85',
            ],
            [
                'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1560448075-bb485b067938?auto=format&fit=crop&w=1400&q=85',
            ],
            [
                'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1620626011761-996317b8d101?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1400&q=85',
                'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1400&q=85',
            ],
        ];
        $videos = [
            'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4',
            'https://www.w3schools.com/html/mov_bbb.mp4',
            'https://media.w3.org/2010/05/sintel/trailer.mp4',
        ];

        $index = abs(crc32($buildingName . $roomNumber)) % count($sets);
        $images = $sets[$index];

        return [
            'image' => $images[0],
            'images' => $images,
            'video' => $videos[$index % count($videos)],
        ];
    }
}
