<?php

namespace Database\Seeders;

use App\Models\AdminActivityLog;
use App\Models\Bill;
use App\Models\Building;
use App\Models\ContactRequest;
use App\Models\Contract;
use App\Models\ElectricWaterLog;
use App\Models\Equipment;
use App\Models\NotificationLog;
use App\Models\Resident;
use App\Models\ResidentRelative;
use App\Models\Review;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomEquipment;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FullDemoSeeder extends Seeder
{
    private array $roles = [];

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedRoles();

            // 1. Tạo Superadmin hệ thống
            User::updateOrCreate(
                ['username' => 'superadmin'],
                [
                    'tenant_id' => null,
                    'role_id' => $this->roles['admin']->id,
                    'name' => 'Admin hệ thống',
                    'phone' => '0999999999',
                    'email' => 'superadmin@smartroom.local',
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'like' => 'Superadmin',
                ]
            );

            // 2. Tạo Tenant & Chủ trọ chưa xác minh kèm yêu cầu KYC mẫu
            $this->seedUnverifiedLandlordWithKyc();

            // 3. Tạo các chủ trọ và phòng trọ mẫu tại 10 khu vực
            foreach ($this->tenantBlueprints() as $tenantIndex => $blueprint) {
                $tenant = $this->seedTenant($blueprint);
                $landlord = $this->seedLandlord($tenant, $blueprint, $tenantIndex);
                $rooms = $this->seedBuildingsAndRooms($tenant, $blueprint);
                $residents = $this->seedResidents($tenant, $rooms, $tenantIndex);

                $this->seedContracts($tenant, $rooms, $residents, $tenantIndex);
                $this->seedUtilitiesAndBills($tenant, $rooms, $residents);
                $equipment = $this->seedEquipment($tenant, $rooms);
                $this->seedTickets($tenant, $rooms, $residents);
                $this->seedReviewsAndContactRequests($rooms, $tenantIndex);
                $this->seedNotifications($tenant, $rooms, $residents);
                $this->seedActivityLogs($tenant, $landlord, $rooms, $residents, $equipment);

                // Tạo thêm tài khoản Manager cho từng Tenant
                $this->seedManager($tenant, $tenantIndex);
            }

            $this->seedGuestUsers();
        });

        $this->printInstructions();
    }

    private function seedRoles(): void
    {
        $roles = [
            'admin' => ['name' => 'Admin hệ thống', 'description' => 'Quản lý toàn bộ hệ thống, xác minh danh tính chủ trọ và kiểm tra báo cáo.'],
            'landlord' => ['name' => 'Chủ trọ / Quản lý', 'description' => 'Quản lý phòng, cư dân, hợp đồng, hóa đơn và thiết bị.'],
            'unverified_landlord' => ['name' => 'Chủ trọ chưa xác minh', 'description' => 'Chủ trọ mới đăng ký, chờ admin xác minh tài khoản.'],
            'manager' => ['name' => 'Nhân viên quản lý', 'description' => 'Nhân viên do chủ trọ bổ nhiệm để quản lý tòa nhà.'],
            'resident' => ['name' => 'Cư dân thuê phòng', 'description' => 'Xem hóa đơn, hợp đồng và gửi yêu cầu báo hỏng.'],
            'guest' => ['name' => 'Khách tìm phòng', 'description' => 'Tìm kiếm phòng và gửi yêu cầu tư vấn.'],
        ];

        foreach ($roles as $slug => $payload) {
            $this->roles[$slug] = Role::updateOrCreate(['slug' => $slug], $payload);
        }
    }

    private function seedUnverifiedLandlordWithKyc(): void
    {
        $tenant = Tenant::updateOrCreate(
            ['email' => 'unverified@demo.smartroom.local'],
            [
                'name' => 'Nhà Trọ Hoàng Gia Đống Đa',
                'phone' => '0888999888',
                'bank_name' => 'MB',
                'bank_account_no' => '999900001111',
                'bank_account_name' => 'HOANG GIA DONG DA',
                'verification_status' => 'unverified',
                'listing_badge' => 'unverified',
                'boost_score' => 0,
                'onboarding_step' => 1,
            ]
        );

        $building = Building::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Hoàng Gia Building Đống Đa'],
            [
                'address' => 'Số 10 Xã Đàn, Đống Đa, Hà Nội',
                'description' => 'Nhà trọ cao cấp trung tâm Đống Đa, đầy đủ tiện ích hiện đại.',
            ]
        );

        foreach (['101', '102'] as $roomNo) {
            Room::updateOrCreate(
                ['building_id' => $building->id, 'room_number' => $roomNo],
                [
                    'tenant_id' => $tenant->id,
                    'floor' => 1,
                    'status' => 'empty',
                    'room_type' => 'vip',
                    'price' => 4500000,
                    'area' => 30,
                    'amenities' => ['điều hòa', 'nóng lạnh', 'wifi', 'ban công', 'wc khép kín', 'cho nuôi thú cưng'],
                    'description' => 'Phòng test cho chủ trọ chưa xác minh.',
                    'image' => null,
                    'images' => [],
                    'video' => null,
                    'version' => 1,
                ]
            );
        }

        $user = User::updateOrCreate(
            ['username' => 'unverified-landlord'],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $this->roles['unverified_landlord']->id,
                'name' => 'Trần Văn Chưa Xác Minh',
                'phone' => '0888999888',
                'email' => 'unverified@demo.smartroom.local',
                'password' => Hash::make('password'),
                'role' => 'unverified_landlord',
                'like' => 'Chủ trọ chưa xác minh',
            ]
        );

        \App\Models\LandlordProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'tenant_id' => $tenant->id,
                'full_name' => $user->name,
                'phone' => $user->phone,
                'property_name' => $tenant->name,
                'property_address' => $building->address,
                'status' => 'unverified',
            ]
        );

        $verificationRequest = \App\Models\LandlordVerificationRequest::updateOrCreate(
            ['tenant_id' => $tenant->id, 'user_id' => $user->id],
            [
                'type' => 'kyc',
                'cccd_number' => '001095006789',
                'admin_review_consent_given' => true,
                'admin_review_consent_at' => Carbon::now(),
                'admin_review_consent_ip' => '127.0.0.1',
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'reject_reason' => null,
            ]
        );

        foreach (['cccd_front' => 'Căn cước mặt trước.jpg', 'cccd_back' => 'Căn cước mặt sau.jpg'] as $docType => $filename) {
            \App\Models\LandlordVerificationDocument::updateOrCreate(
                ['verification_request_id' => $verificationRequest->id, 'document_type' => $docType],
                [
                    'disk' => 'local',
                    'file_path' => 'kyc/' . $docType . '_demo.jpg',
                    'original_filename' => $filename,
                    'mime_type' => 'image/jpeg',
                    'size_bytes' => 102400,
                    'sha256_checksum' => hash('sha256', $filename),
                    'status' => 'pending',
                ]
            );
        }
    }

    private function seedTenant(array $blueprint): Tenant
    {
        return Tenant::updateOrCreate(
            ['email' => $blueprint['email']],
            [
                'name' => $blueprint['name'],
                'phone' => $blueprint['phone'],
                'bank_name' => $blueprint['bank_name'],
                'bank_account_no' => $blueprint['bank_account_no'],
                'bank_account_name' => $blueprint['bank_account_name'],
                'verification_status' => 'kyc_verified',
                'listing_badge' => 'kyc_verified',
            ]
        );
    }

    private function seedLandlord(Tenant $tenant, array $blueprint, int $tenantIndex): User
    {
        $username = $blueprint['username'] ?? 'demo-landlord-' . ($tenantIndex + 1);
        $email = $blueprint['email'] ?? 'landlord' . ($tenantIndex + 1) . '@demo.smartroom.local';

        // Chủ trọ chính
        $landlord1 = User::updateOrCreate(
            ['username' => $username],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $this->roles['landlord']->id,
                'name' => $blueprint['owner_name'],
                'phone' => $blueprint['phone'] ?? ('0888000' . str_pad((string) ($tenantIndex + 1), 3, '0', STR_PAD_LEFT)),
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'landlord',
                'like' => 'Chủ trọ demo chính',
            ]
        );

        // Chủ trọ phụ (Co-owner)
        User::updateOrCreate(
            ['username' => 'demo-landlord-' . ($tenantIndex + 1) . '-co'],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $this->roles['landlord']->id,
                'name' => $blueprint['owner_name'] . ' (Đồng sở hữu)',
                'phone' => '0888001' . str_pad((string) ($tenantIndex + 1), 3, '0', STR_PAD_LEFT),
                'email' => 'landlord' . ($tenantIndex + 1) . 'co@demo.smartroom.local',
                'password' => Hash::make('password'),
                'role' => 'landlord',
                'like' => 'Chủ trọ đồng sở hữu',
            ]
        );

        return $landlord1;
    }

    private function seedManager(Tenant $tenant, int $tenantIndex): void
    {
        // Manager 1
        User::updateOrCreate(
            ['username' => 'demo-manager-' . ($tenantIndex + 1) . '-1'],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $this->roles['manager']->id,
                'name' => 'Quản lý ' . $tenant->name . ' 1',
                'phone' => '0777000' . str_pad((string) (($tenantIndex + 1) * 10 + 1), 3, '0', STR_PAD_LEFT),
                'email' => 'manager' . ($tenantIndex + 1) . '-1@demo.smartroom.local',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'like' => 'Nhân viên quản lý demo 1',
            ]
        );

        // Manager 2
        User::updateOrCreate(
            ['username' => 'demo-manager-' . ($tenantIndex + 1) . '-2'],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $this->roles['manager']->id,
                'name' => 'Quản lý ' . $tenant->name . ' 2',
                'phone' => '0777000' . str_pad((string) (($tenantIndex + 1) * 10 + 2), 3, '0', STR_PAD_LEFT),
                'email' => 'manager' . ($tenantIndex + 1) . '-2@demo.smartroom.local',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'like' => 'Nhân viên quản lý demo 2',
            ]
        );
    }

    private function seedBuildingsAndRooms(Tenant $tenant, array $blueprint): array
    {
        $rooms = [];

        foreach ($blueprint['buildings'] as $buildingData) {
            $building = Building::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $buildingData['name']],
                [
                    'address' => $buildingData['address'],
                    'description' => $buildingData['description'],
                ]
            );

            foreach ($buildingData['rooms'] as $roomData) {
                $room = Room::updateOrCreate(
                    ['building_id' => $building->id, 'room_number' => $roomData['room_number']],
                    [
                        'tenant_id' => $tenant->id,
                        'floor' => $roomData['floor'],
                        'status' => $roomData['status'],
                        'room_type' => $roomData['room_type'],
                        'price' => $roomData['price'],
                        'area' => $roomData['area'],
                        'amenities' => $roomData['amenities'],
                        'description' => $buildingData['name'] . ' - phòng ' . $roomData['room_number'] . ' phục vụ test đầy đủ trạng thái.',
                        'image' => null,
                        'images' => [],
                        'video' => null,
                        'version' => 1,
                    ]
                );

                $rooms[$buildingData['code'] . '-' . $roomData['room_number']] = $room;
            }
        }

        return $rooms;
    }

    private function seedResidents(Tenant $tenant, array $rooms, int $tenantIndex): array
    {
        $primaryResidents = [];
        $occupiedRooms = collect($rooms)
            ->filter(fn (Room $room) => in_array($room->status, ['occupied', 'overdue'], true))
            ->values();

        foreach ($occupiedRooms as $index => $room) {
            $seedNo = (($tenantIndex + 1) * 100) + $index + 1;

            // 1. Tạo cư dân chính (đứng tên hợp đồng, nhận hóa đơn)
            $name1 = $this->residentNames()[$index % count($this->residentNames())];
            $email1 = 'resident' . $seedNo . '-1@demo.smartroom.local';

            $user1 = User::updateOrCreate(
                ['username' => 'demo-resident-' . $seedNo . '-1'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $this->roles['resident']->id,
                    'name' => $name1,
                    'phone' => '0777' . str_pad((string) ($seedNo * 10 + 1), 6, '0', STR_PAD_LEFT),
                    'email' => $email1,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'like' => 'Khách thuê chính demo',
                ]
            );

            $resident1 = Resident::updateOrCreate(
                ['email' => $email1],
                [
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'user_id' => $user1->id,
                    'name' => $name1,
                    'dob' => Carbon::create(1992 + ($index % 10), ($index % 12) + 1, 10)->toDateString(),
                    'phone' => $user1->phone,
                    'cccd' => '001' . str_pad((string) ($seedNo * 10 + 1), 9, '0', STR_PAD_LEFT),
                    'hometown' => ['Hà Nội', 'Nam Định', 'Thái Bình', 'Bắc Ninh', 'Đà Nẵng'][$index % 5],
                    'start_date' => Carbon::today()->subMonths(12 - ($index % 6))->toDateString(),
                    'status' => 'active',
                    'temporary_residence_status' => ['registered', 'none', 'absent'][$index % 3],
                    'version' => 1,
                ]
            );

            // 2. Tạo cư dân ở ghép (bạn chung phòng, có tài khoản đăng nhập riêng)
            $name2 = $this->residentNames()[($index + 1) % count($this->residentNames())] . ' (Ở Ghép)';
            $email2 = 'resident' . $seedNo . '-2@demo.smartroom.local';

            $user2 = User::updateOrCreate(
                ['username' => 'demo-resident-' . $seedNo . '-2'],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $this->roles['resident']->id,
                    'name' => $name2,
                    'phone' => '0777' . str_pad((string) ($seedNo * 10 + 2), 6, '0', STR_PAD_LEFT),
                    'email' => $email2,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'like' => 'Khách ở ghép demo',
                ]
            );

            Resident::updateOrCreate(
                ['email' => $email2],
                [
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'user_id' => $user2->id,
                    'name' => $name2,
                    'dob' => Carbon::create(1994 + ($index % 8), (($index + 4) % 12) + 1, 15)->toDateString(),
                    'phone' => $user2->phone,
                    'cccd' => '001' . str_pad((string) ($seedNo * 10 + 2), 9, '0', STR_PAD_LEFT),
                    'hometown' => ['Thanh Hóa', 'Nghệ An', 'Hải Phòng', 'Quảng Ninh', 'Lạng Sơn'][$index % 5],
                    'start_date' => Carbon::today()->subMonths(6 - ($index % 3))->toDateString(),
                    'status' => 'active',
                    'temporary_residence_status' => ['registered', 'none', 'absent'][($index + 1) % 3],
                    'version' => 1,
                ]
            );

            if ($index % 3 === 0) {
                ResidentRelative::updateOrCreate(
                    ['resident_id' => $resident1->id, 'name' => 'Người thân ' . $room->room_number],
                    [
                        'dob' => Carbon::create(1998, 6, 15)->toDateString(),
                        'cccd' => 'REL' . str_pad((string) $seedNo, 9, '0', STR_PAD_LEFT),
                        'phone' => '0666' . str_pad((string) $seedNo, 6, '0', STR_PAD_LEFT),
                        'hometown' => 'Hà Nội',
                        'relationship' => 'Anh/Chị/Em',
                        'temporary_residence_status' => 'registered',
                        'start_date' => Carbon::today()->subDays(20)->toDateString(),
                        'end_date' => Carbon::today()->addMonths(2)->toDateString(),
                        'version' => 1,
                    ]
                );
            }

            $primaryResidents[$room->id] = $resident1;
        }

        return $primaryResidents;
    }

    private function seedContracts(Tenant $tenant, array $rooms, array $residents, int $tenantIndex): void
    {
        foreach ($residents as $roomId => $resident) {
            $room = collect($rooms)->firstWhere('id', $roomId);
            if (!$room) {
                continue;
            }

            $endingSoon = ((int) substr($room->room_number, -1)) % 4 === 0;
            Contract::updateOrCreate(
                ['contract_code' => 'DEMO-HD-T' . ($tenantIndex + 1) . '-' . $room->id],
                [
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'resident_id' => $resident->id,
                    'start_date' => $resident->start_date,
                    'end_date' => $endingSoon
                        ? Carbon::today()->addDays(18)->toDateString()
                        : Carbon::parse($resident->start_date)->addYear()->toDateString(),
                    'deposit' => $room->price,
                    'status' => 'active',
                    'terms' => 'Hợp đồng demo cho phòng ' . $room->room_number . '. Thanh toán trước ngày 10 hằng tháng.',
                    'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=',
                ]
            );
        }
    }

    private function seedUtilitiesAndBills(Tenant $tenant, array $rooms, array $residents): void
    {
        $months = [
            Carbon::today()->subMonths(2)->format('Y-m'),
            Carbon::today()->subMonth()->format('Y-m'),
            Carbon::today()->format('Y-m'),
        ];

        foreach ($rooms as $room) {
            if (!isset($residents[$room->id])) {
                continue;
            }

            foreach ($months as $monthIndex => $month) {
                $electricUsage = 70 + (($room->id + $monthIndex) % 65);
                $waterUsage = 4 + (($room->id + $monthIndex) % 8);
                $oldElectric = 800 + ($room->id * 7) + ($monthIndex * 130);
                $oldWater = 100 + ($room->id % 20) + ($monthIndex * 10);
                $electricPrice = $tenant->bank_name === 'VCB' ? 3800 : 3500;
                $waterPrice = $tenant->bank_name === 'VCB' ? 18000 : 15000;
                $serviceCost = 150000;
                $total = $room->price + ($electricUsage * $electricPrice) + ($waterUsage * $waterPrice) + $serviceCost;
                $isCurrent = $month === Carbon::today()->format('Y-m');
                $billStatus = $isCurrent ? ($room->status === 'overdue' ? 'overdue' : 'pending') : 'paid';
                $paymentDate = $billStatus === 'paid' ? Carbon::parse($month . '-10')->addDays($room->id % 5) : null;

                $log = ElectricWaterLog::updateOrCreate(
                    ['room_id' => $room->id, 'billing_month' => $month],
                    [
                        'tenant_id' => $tenant->id,
                        'old_electricity' => $oldElectric,
                        'new_electricity' => $oldElectric + $electricUsage,
                        'old_water' => $oldWater,
                        'new_water' => $oldWater + $waterUsage,
                        'electricity_price' => $electricPrice,
                        'water_price' => $waterPrice,
                    ]
                );

                Bill::updateOrCreate(
                    ['room_id' => $room->id, 'billing_month' => $month],
                    [
                        'tenant_id' => $tenant->id,
                        'electric_water_log_id' => $log->id,
                        'room_price' => $room->price,
                        'electricity_usage' => $electricUsage,
                        'electricity_cost' => $electricUsage * $electricPrice,
                        'water_usage' => $waterUsage,
                        'water_cost' => $waterUsage * $waterPrice,
                        'service_cost' => $serviceCost,
                        'total_amount' => $total,
                        'status' => $billStatus,
                        'payment_date' => $paymentDate,
                        'vietqr_url' => $this->vietQrUrl($tenant, $room, $month, $total),
                    ]
                );

                UtilityRecord::updateOrCreate(
                    ['room_id' => $room->id, 'billing_month' => $month],
                    [
                        'tenant_id' => $tenant->id,
                        'old_electricity' => $oldElectric,
                        'new_electricity' => $oldElectric + $electricUsage,
                        'old_water' => $oldWater,
                        'new_water' => $oldWater + $waterUsage,
                        'electricity_price' => $electricPrice,
                        'water_price' => $waterPrice,
                        'status' => $billStatus === 'pending' ? 'sent' : $billStatus,
                        'payment_date' => $paymentDate,
                        'payment_method' => $paymentDate ? ['cash', 'bank_transfer', 'vietqr'][$room->id % 3] : null,
                    ]
                );
            }
        }
    }

    private function seedEquipment(Tenant $tenant, array $rooms): array
    {
        $equipmentItems = [
            ['code' => 'AC', 'name' => 'Điều hòa 9000 BTU', 'unit' => 'cái', 'quantity' => 40],
            ['code' => 'WM', 'name' => 'Máy giặt mini', 'unit' => 'cái', 'quantity' => 20],
            ['code' => 'FR', 'name' => 'Tủ lạnh 90L', 'unit' => 'cái', 'quantity' => 30],
            ['code' => 'BED', 'name' => 'Giường sắt 1m2', 'unit' => 'cái', 'quantity' => 50],
            ['code' => 'LOCK', 'name' => 'Khóa vân tay', 'unit' => 'cái', 'quantity' => 40],
            ['code' => 'CAM', 'name' => 'Camera hành lang', 'unit' => 'cái', 'quantity' => 20],
        ];

        $equipment = [];
        foreach ($equipmentItems as $item) {
            $equipment[$item['code']] = Equipment::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $item['code']],
                [
                    'name' => $item['name'],
                    'unit' => $item['unit'],
                    'total_quantity' => $item['quantity'],
                    'allocated_quantity' => 0,
                    'description' => 'Demo equipment for full test data.',
                    'version' => 1,
                ]
            );
        }

        RoomEquipment::where('tenant_id', $tenant->id)->delete();

        foreach (array_values($rooms) as $index => $room) {
            if ($room->status === 'empty') {
                continue;
            }

            foreach (['AC' => 1, 'BED' => 1, 'LOCK' => 1] as $code => $quantity) {
                $this->allocateEquipment($tenant, $room, $equipment[$code], $quantity);
            }
            if ($index % 2 === 0) {
                $this->allocateEquipment($tenant, $room, $equipment['FR'], 1);
            }
            if ($index % 3 === 0) {
                $this->allocateEquipment($tenant, $room, $equipment['WM'], 1);
            }
        }

        foreach ($equipment as $item) {
            $item->update([
                'allocated_quantity' => RoomEquipment::where('equipment_id', $item->id)->sum('quantity'),
                'version' => $item->version + 1,
            ]);
        }

        return $equipment;
    }

    private function allocateEquipment(Tenant $tenant, Room $room, Equipment $equipment, int $quantity): void
    {
        RoomEquipment::updateOrCreate(
            ['room_id' => $room->id, 'equipment_id' => $equipment->id],
            [
                'tenant_id' => $tenant->id,
                'quantity' => $quantity,
                'last_allocated_at' => Carbon::today()->subDays($room->id % 20),
            ]
        );
    }

    private function seedTickets(Tenant $tenant, array $rooms, array $residents): void
    {
        foreach (array_values($rooms) as $index => $room) {
            if (!isset($residents[$room->id]) || $index % 3 !== 0) {
                continue;
            }

            Ticket::updateOrCreate(
                ['room_id' => $room->id, 'title' => 'Báo hỏng phòng ' . $room->room_number],
                [
                    'tenant_id' => $tenant->id,
                    'resident_id' => $residents[$room->id]->id,
                    'description' => ['Điều hòa không mát.', 'Vòi sen bị rò nước.', 'Khóa cửa bị kẹt.'][$index % 3],
                    'category' => ['điện', 'nước', 'nội thất'][$index % 3],
                    'status' => ['pending', 'processing', 'resolved'][$index % 3],
                    'assigned_to' => $index % 3 === 0 ? null : 'Tổ bảo trì',
                ]
            );
        }
    }

    private function seedReviewsAndContactRequests(array $rooms, int $tenantIndex): void
    {
        foreach (array_values($rooms) as $index => $room) {
            if ($index < 5) {
                Review::updateOrCreate(
                    ['room_id' => $room->id, 'author_name' => 'Khách tham quan ' . ($tenantIndex + 1) . '-' . ($index + 1)],
                    [
                        'rating' => 3 + ($index % 3),
                        'comment' => 'Phòng sạch, vị trí thuận tiện, phù hợp để test đánh giá công khai.',
                    ]
                );
            }

            if ($room->status === 'empty') {
                ContactRequest::updateOrCreate(
                    ['room_id' => $room->id, 'phone' => '0555' . str_pad((string) ($tenantIndex * 100 + $index), 6, '0', STR_PAD_LEFT)],
                    [
                        'name' => 'Khách cần tư vấn ' . $room->room_number,
                        'message' => 'Tôi muốn xem phòng và hỏi về chi phí cọc.',
                        'status' => $index % 2 === 0 ? 'pending' : 'processed',
                    ]
                );
            }
        }
    }

    private function seedNotifications(Tenant $tenant, array $rooms, array $residents): void
    {
        NotificationLog::where('tenant_id', $tenant->id)->where('meta->seeded', true)->delete();

        foreach (array_values($residents) as $index => $resident) {
            $room = collect($rooms)->firstWhere('id', $resident->room_id);
            NotificationLog::create([
                'tenant_id' => $tenant->id,
                'type' => $index % 2 === 0 ? 'payment_reminder' : 'contract_notice',
                'channel' => ['zalo', 'email', 'sms'][$index % 3],
                'recipient_name' => $resident->name,
                'recipient_contact' => $resident->phone,
                'subject' => 'Thông báo phòng ' . ($room->room_number ?? 'N/A'),
                'message' => 'Dữ liệu demo thông báo cho cư dân ' . $resident->name,
                'status' => ['sent', 'failed', 'queued'][$index % 3],
                'target_type' => Resident::class,
                'target_id' => $resident->id,
                'meta' => ['seeded' => true, 'room_number' => $room->room_number ?? null],
                'sent_at' => Carbon::now()->subDays($index),
            ]);
        }
    }

    private function seedActivityLogs(Tenant $tenant, User $landlord, array $rooms, array $residents, array $equipment): void
    {
        AdminActivityLog::where('tenant_id', $tenant->id)->where('metadata->seeded', true)->delete();

        $subjects = [
            ['create', 'rooms', 'Tạo phòng demo đầu tiên', reset($rooms)],
            ['create', 'residents', 'Thêm cư dân vào phòng demo', reset($residents) ?: null],
            ['create', 'utilities', 'Chốt điện nước tháng hiện tại', UtilityRecord::where('tenant_id', $tenant->id)->latest()->first()],
            ['payment', 'payments', 'Ghi nhận thanh toán demo', UtilityRecord::where('tenant_id', $tenant->id)->where('status', 'paid')->latest()->first()],
            ['allocate', 'equipment', 'Bàn giao thiết bị demo', reset($equipment) ?: null],
            ['notify', 'notifications', 'Gửi thông báo nhắc nợ demo', NotificationLog::where('tenant_id', $tenant->id)->latest()->first()],
        ];

        foreach ($subjects as $index => [$action, $module, $description, $subject]) {
            AdminActivityLog::create([
                'tenant_id' => $tenant->id,
                'user_id' => $landlord->id,
                'user_name' => $landlord->name,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'ip_address' => '127.0.0.1',
                'method' => 'SEED',
                'url' => '/full-seeder',
                'user_agent' => 'FullDemoSeeder',
                'before_values' => null,
                'after_values' => $subject ? ['id' => $subject->getKey()] : null,
                'metadata' => ['seeded' => true, 'index' => $index],
                'created_at' => Carbon::now()->subHours(24 - $index),
                'updated_at' => Carbon::now()->subHours(24 - $index),
            ]);
        }
    }

    private function seedGuestUsers(): void
    {
        User::updateOrCreate(
            ['username' => 'demo-guest'],
            [
                'tenant_id' => null,
                'role_id' => $this->roles['guest']->id,
                'name' => 'Khách vãng lai',
                'phone' => '0999000001',
                'email' => 'guest@demo.smartroom.local',
                'password' => Hash::make('password'),
                'role' => 'guest',
                'like' => 'Khách tìm phòng',
            ]
        );
    }

    private function vietQrUrl(Tenant $tenant, Room $room, string $month, int $amount): string
    {
        $info = 'Thanh toán phòng ' . $room->room_number . ' tháng ' . substr($month, 5, 2);

        return 'https://img.vietqr.io/image/' . $tenant->bank_name . '-' . $tenant->bank_account_no
            . '-compact.png?amount=' . $amount
            . '&addInfo=' . rawurlencode($info)
            . '&accountName=' . rawurlencode($tenant->bank_account_name ?? $tenant->name);
    }

    private function tenantBlueprints(): array
    {
        return [
            [
                'name' => 'Demo SmartRoom Cầu Giấy',
                'email' => 'demo-caugiay@smartroom.local',
                'phone' => '0988000001',
                'owner_name' => 'Nguyễn Chủ Trọ Cầu Giấy',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880001',
                'bank_account_name' => 'NGUYỄN CHỦ TRỌ CẦU GIẤY',
                'buildings' => $this->buildingBlueprints('CG', 'Cầu Giấy', 3200000),
            ],
            [
                'name' => 'Demo Renty Thanh Xuân',
                'email' => 'demo-thanhxuan@smartroom.local',
                'phone' => '0988000002',
                'owner_name' => 'Lê Quản Lý Thanh Xuân',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880002',
                'bank_account_name' => 'LÊ QUẢN LÝ THANH XUÂN',
                'buildings' => $this->buildingBlueprints('TX', 'Thanh Xuân', 4000000),
            ],
            [
                'name' => 'Demo Studio Quận 10',
                'email' => 'demo-quan10@smartroom.local',
                'phone' => '0988000003',
                'owner_name' => 'Trần Quản Lý Quận 10',
                'bank_name' => 'ACB',
                'bank_account_no' => '999988880003',
                'bank_account_name' => 'TRẦN QUẢN LÝ QUẬN 10',
                'buildings' => $this->buildingBlueprints('Q10', 'Quận 10', 5200000),
            ],
            [
                'name' => 'Demo Nhà Trọ Xanh Đống Đa',
                'email' => 'demo-dongda@smartroom.local',
                'phone' => '0988000004',
                'owner_name' => 'Phạm Văn Đống Đa',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880004',
                'bank_account_name' => 'PHAM VAN DONG DA',
                'buildings' => $this->buildingBlueprints('DD', 'Đống Đa', 3500000),
            ],
            [
                'name' => 'Demo Căn Hộ Hai Bà Trưng',
                'email' => 'demo-hbt@smartroom.local',
                'phone' => '0988000005',
                'owner_name' => 'Vũ Thị Hai Bà Trưng',
                'bank_name' => 'VCB',
                'bank_account_no' => '999988880005',
                'bank_account_name' => 'VU THI HAI BA TRUNG',
                'buildings' => $this->buildingBlueprints('HBT', 'Hai Bà Trưng', 4500000),
            ],
            [
                'name' => 'Demo Homestay Tây Hồ',
                'email' => 'demo-tayho@smartroom.local',
                'phone' => '0988000006',
                'owner_name' => 'Hoàng Văn Tây Hồ',
                'bank_name' => 'Agribank',
                'bank_account_no' => '999988880006',
                'bank_account_name' => 'HOANG VAN TAY HO',
                'buildings' => $this->buildingBlueprints('TH', 'Tây Hồ', 6000000),
            ],
            [
                'name' => 'Demo Luxury Quận 1',
                'email' => 'demo-quan1@smartroom.local',
                'phone' => '0988000007',
                'owner_name' => 'Ngô Thị Quận 1',
                'bank_name' => 'VietinBank',
                'bank_account_no' => '999988880007',
                'bank_account_name' => 'NGO THI QUAN 1',
                'buildings' => $this->buildingBlueprints('Q1', 'Quận 1', 8000000),
            ],
            [
                'name' => 'Demo Căn Hộ Bình Thạnh',
                'email' => 'demo-binhthanh@smartroom.local',
                'phone' => '0988000008',
                'owner_name' => 'Bùi Văn Bình Thạnh',
                'bank_name' => 'Sacombank',
                'bank_account_no' => '999988880008',
                'bank_account_name' => 'BUI VAN BINH THANH',
                'buildings' => $this->buildingBlueprints('BT', 'Bình Thạnh', 4800000),
            ],
            [
                'name' => 'Demo Phòng Trọ Tân Bình',
                'email' => 'demo-tanbinh@smartroom.local',
                'phone' => '0988000009',
                'owner_name' => 'Đặng Văn Tân Bình',
                'bank_name' => 'Techcombank',
                'bank_account_no' => '999988880009',
                'bank_account_name' => 'DANG VAN TAN BINH',
                'buildings' => $this->buildingBlueprints('TB', 'Tân Bình', 3800000),
            ],
            [
                'name' => 'Demo Phòng Trọ Ba Đình',
                'email' => 'demo-badinh@smartroom.local',
                'phone' => '0988000010',
                'owner_name' => 'Trần Văn Ba Đình',
                'bank_name' => 'BIDV',
                'bank_account_no' => '999988880010',
                'bank_account_name' => 'TRAN VAN BA DINH',
                'buildings' => $this->buildingBlueprints('BD', 'Ba Đình', 4200000),
            ],
            [
                'name' => 'Căn hộ dịch vụ Luxury Bình Thạnh',
                'email' => 'admin-hcm@smartroom.local',
                'phone' => '0909123456',
                'owner_name' => 'Trần Văn Hoàng',
                'username' => 'admin-hcm',
                'bank_name' => 'VCB',
                'bank_account_no' => '1012345678',
                'bank_account_name' => 'TRAN VAN HOANG',
                'buildings' => [
                    [
                        'code' => 'BTA',
                        'name' => 'Chung cư mini Luxury Điện Biên Phủ',
                        'address' => '12 Điện Biên Phủ, Phường 15, Quận Bình Thạnh, TP. Hồ Chí Minh',
                        'description' => 'Tòa nhà căn hộ cao cấp đầy đủ tiện nghi, thang máy, bảo vệ 24/7, gần Ngã tư Hàng Xanh.',
                        'rooms' => $this->roomBlueprints(6000000),
                    ],
                    [
                        'code' => 'BTB',
                        'name' => 'Nhà trọ Studio Nguyễn Gia Trí',
                        'address' => '88/12 Nguyễn Gia Trí, Phường 25, Quận Bình Thạnh, TP. Hồ Chí Minh',
                        'description' => 'Khu nhà trọ Studio sinh viên cao cấp, gần Đại học HUTECH, Ngoại Thương, Giao thông Vận tải.',
                        'rooms' => $this->roomBlueprints(5000000),
                    ]
                ],
            ],
            [
                'name' => 'Demo Phòng Trọ Quận 7 Phú Mỹ Hưng',
                'email' => 'demo-quan7@smartroom.local',
                'phone' => '0988000012',
                'owner_name' => 'Lý Minh Quận 7',
                'bank_name' => 'ACB',
                'bank_account_no' => '999988880012',
                'bank_account_name' => 'LY MINH QUAN 7',
                'buildings' => [
                    [
                        'code' => 'Q7A',
                        'name' => 'Căn hộ dịch vụ Sky Garden PMH',
                        'address' => '25 Nguyễn Lương Bằng, Phường Tân Phú, Quận 7, TP. Hồ Chí Minh',
                        'description' => 'Căn hộ dịch vụ cao cấp khu Phú Mỹ Hưng, gần Lotte Mart, SC VivoCity và bệnh viện FV.',
                        'rooms' => $this->roomBlueprints(7500000),
                    ],
                    [
                        'code' => 'Q7B',
                        'name' => 'Nhà trọ sinh viên Tôn Thất Thuyết',
                        'address' => '112/5 Tôn Thất Thuyết, Phường 16, Quận 4, TP. Hồ Chí Minh',
                        'description' => 'Nhà trọ giá rẻ gần cầu Kênh Tẻ, thuận tiện qua Quận 7 và Quận 1, phù hợp sinh viên.',
                        'rooms' => $this->roomBlueprints(3500000),
                    ],
                ],
            ],
            [
                'name' => 'Demo Nhà Trọ Thủ Đức Làng ĐH',
                'email' => 'demo-thuduc@smartroom.local',
                'phone' => '0988000013',
                'owner_name' => 'Nguyễn Hữu Thủ Đức',
                'bank_name' => 'MB',
                'bank_account_no' => '999988880013',
                'bank_account_name' => 'NGUYEN HUU THU DUC',
                'buildings' => [
                    [
                        'code' => 'TDA',
                        'name' => 'Khu trọ Làng Đại Học Thủ Đức',
                        'address' => '18 Đường Số 7, Khu phố 6, Phường Linh Trung, TP. Thủ Đức, TP. Hồ Chí Minh',
                        'description' => 'Nhà trọ sinh viên giá rẻ ngay Làng Đại học Quốc gia, gần ĐH Bách Khoa, ĐH KHTN, ĐH Nông Lâm.',
                        'rooms' => $this->roomBlueprints(2800000),
                    ],
                    [
                        'code' => 'TDB',
                        'name' => 'Studio cao cấp Xa lộ Hà Nội',
                        'address' => '215 Xa lộ Hà Nội, Phường Trường Thọ, TP. Thủ Đức, TP. Hồ Chí Minh',
                        'description' => 'Khu căn hộ mini tiện nghi gần trạm Metro Bến Xe Miền Đông, thuận lợi di chuyển toàn thành phố.',
                        'rooms' => $this->roomBlueprints(4200000),
                    ],
                ],
            ],
            [
                'name' => 'Demo Phòng Trọ Gò Vấp',
                'email' => 'demo-govap@smartroom.local',
                'phone' => '0988000014',
                'owner_name' => 'Trần Thị Gò Vấp',
                'bank_name' => 'Sacombank',
                'bank_account_no' => '999988880014',
                'bank_account_name' => 'TRAN THI GO VAP',
                'buildings' => [
                    [
                        'code' => 'GVA',
                        'name' => 'Nhà trọ Nguyễn Oanh Gò Vấp',
                        'address' => '45/3 Nguyễn Oanh, Phường 17, Quận Gò Vấp, TP. Hồ Chí Minh',
                        'description' => 'Nhà trọ mới xây thoáng mát, gần chợ Gò Vấp, ĐH Công nghiệp TP.HCM và ĐH Văn Lang.',
                        'rooms' => $this->roomBlueprints(3200000),
                    ],
                    [
                        'code' => 'GVB',
                        'name' => 'Căn hộ mini Phan Văn Trị',
                        'address' => '200 Phan Văn Trị, Phường 11, Quận Gò Vấp, TP. Hồ Chí Minh',
                        'description' => 'Căn hộ mini đầy đủ tiện nghi, gần Emart Gò Vấp, Công viên Gia Định, thuận tiện xe buýt.',
                        'rooms' => $this->roomBlueprints(3800000),
                    ],
                ],
            ],
        ];
    }

    private function buildingBlueprints(string $code, string $areaName, int $basePrice): array
    {
        return [
            [
                'code' => $code . 'A',
                'name' => 'Demo ' . $areaName . ' A',
                'address' => 'Số 12 đường demo ' . $areaName,
                'description' => 'Tòa nhà demo đầy đủ phòng trống, có khách, quá hạn và bảo trì.',
                'rooms' => $this->roomBlueprints($basePrice),
            ],
            [
                'code' => $code . 'B',
                'name' => 'Demo ' . $areaName . ' B',
                'address' => 'Số 88 ngõ demo ' . $areaName,
                'description' => 'Tòa nhà demo thứ hai để test lọc theo tòa nhà và tenant.',
                'rooms' => $this->roomBlueprints($basePrice + 450000),
            ],
        ];
    }

    private function roomBlueprints(int $basePrice): array
    {
        $statuses = [
            'occupied', 'occupied', 'overdue', 'empty', 'maintenance', 'occupied',
            'empty', 'occupied', 'empty', 'occupied', 'empty', 'occupied'
        ];
        $amenitiesPool = [
            ['điều hòa', 'nóng lạnh', 'wifi', 'cho nuôi thú cưng', 'gác lửng', 'wc khép kín'],
            ['điều hòa', 'nóng lạnh', 'ban công', 'wc khép kín', 'tủ quần áo'],
            ['điều hòa', 'gác lửng', 'wc khép kín', 'wifi'],
            ['điều hòa', 'nóng lạnh', 'ban công', 'cho nuôi thú cưng', 'wc khép kín'],
            ['nóng lạnh', 'wifi', 'tủ lạnh', 'gác lửng'],
            ['điều hòa', 'ban công', 'gác lửng', 'wc khép kín', 'cho nuôi thú cưng', 'wifi'],
            ['điều hòa', 'nóng lạnh', 'wifi', 'ban công'],
            ['nóng lạnh', 'wifi', 'tủ lạnh'],
            ['điều hòa', 'gác lửng', 'wc khép kín'],
            ['điều hòa', 'nóng lạnh', 'cho nuôi thú cưng'],
            ['nóng lạnh', 'wifi', 'gác lửng'],
            ['điều hòa', 'ban công', 'wc khép kín']
        ];

        return collect(['101', '102', '201', '202', '301', '302', '401', '402', '501', '502', '601', '602'])
            ->map(function (string $roomNumber, int $index) use ($basePrice, $statuses, $amenitiesPool) {
                return [
                    'room_number' => $roomNumber,
                    'floor' => (int) substr($roomNumber, 0, 1),
                    'status' => $statuses[$index],
                    'room_type' => $index % 3 === 0 ? 'vip' : 'normal',
                    'price' => $basePrice + ($index * 150000),
                    'area' => 20 + ($index * 2),
                    'amenities' => $amenitiesPool[$index % count($amenitiesPool)],
                ];
            })
            ->all();
    }

    private function residentNames(): array
    {
        return [
            'Trần Minh Anh',
            'Nguyễn Hoàng Nam',
            'Lê Thu Trang',
            'Phạm Quốc Việt',
            'Đỗ Khánh Linh',
            'Bùi Tiến Đạt',
            'Hoàng Gia Bảo',
            'Vũ Phương Thảo',
            'Đặng Anh Đức',
            'Ngô Bảo Châu',
        ];
    }

    private function printInstructions(): void
    {
        if (isset($this->command)) {
            $this->command->info("\n=======================================================================");
            $this->command->info("   DỮ LIỆU SEED CHO HỆ THỐNG QUẢN LÝ NHÀ TRỌ ĐÃ SẴN SÀNG ĐỂ KIỂM THỬ");
            $this->command->info("=======================================================================");
            $this->command->info("1. ADMIN HỆ THỐNG (Superadmin):");
            $this->command->info("   - Username: superadmin");
            $this->command->info("   - Password: password");
            $this->command->info("   - Vai trò: Quản lý toàn hệ thống, phê duyệt KYC chủ trọ.");
            $this->command->info("-----------------------------------------------------------------------");
            $this->command->info("2. CHỦ TRỌ CHƯA XÁC MINH (Chờ duyệt KYC):");
            $this->command->info("   - Username: unverified-landlord");
            $this->command->info("   - Password: password");
            $this->command->info("   - Vai trò: Đăng ký phòng trọ mới, tải tài liệu KYC chờ admin duyệt.");
            $this->command->info("-----------------------------------------------------------------------");
            $this->command->info("3. CÁC CHỦ TRỌ MẪU (Đã xác minh):");
            $this->command->info("   - Username: demo-landlord-1 & demo-landlord-1-co, demo-landlord-2 & demo-landlord-2-co, ...");
            $this->command->info("   - Password: password");
            $this->command->info("   - Vai trò: Chủ trọ chính và đồng sở hữu quản lý tòa nhà, phòng trọ.");
            $this->command->info("-----------------------------------------------------------------------");
            $this->command->info("4. NHÂN VIÊN QUẢN LÝ (Staff/Manager):");
            $this->command->info("   - Username: demo-manager-1-1 & demo-manager-1-2, demo-manager-2-1 & demo-manager-2-2, ...");
            $this->command->info("   - Password: password");
            $this->command->info("   - Vai trò: Nhân viên hỗ trợ chủ trọ quản lý vận hành tòa nhà.");
            $this->command->info("-----------------------------------------------------------------------");
            $this->command->info("5. CÁC CƯ DÂN THUÊ PHÒNG (Resident):");
            $this->command->info("   - Username: demo-resident-101-1 (cư dân chính) & demo-resident-101-2 (ở ghép), ...");
            $this->command->info("   - Password: password");
            $this->command->info("   - Vai trò: Xem hoá đơn, lịch sử thanh toán, gửi sự cố báo hỏng.");
            $this->command->info("-----------------------------------------------------------------------");
            $this->command->info("6. KHÁCH TÌM PHÒNG (Guest):");
            $this->command->info("   - Username: demo-guest");
            $this->command->info("   - Password: password");
            $this->command->info("   - Vai trò: Xem phòng, tìm phòng, gửi bình luận đánh giá phòng trọ.");
            $this->command->info("=======================================================================\n");
        }
    }
}
