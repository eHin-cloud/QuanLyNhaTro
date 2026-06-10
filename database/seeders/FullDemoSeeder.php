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
            }

            $this->seedGuestUsers();
        });
    }

    private function seedRoles(): void
    {
        $roles = [
            'landlord' => ['name' => 'Chủ trọ / Quản lý', 'description' => 'Quản lý phòng, cư dân, hợp đồng, hóa đơn và thiết bị.'],
            'resident' => ['name' => 'Cư dân thuê phòng', 'description' => 'Xem hóa đơn, hợp đồng và gửi yêu cầu báo hỏng.'],
            'guest' => ['name' => 'Khách tìm phòng', 'description' => 'Tìm kiếm phòng và gửi yêu cầu tư vấn.'],
        ];

        foreach ($roles as $slug => $payload) {
            $this->roles[$slug] = Role::updateOrCreate(['slug' => $slug], $payload);
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
            ]
        );
    }

    private function seedLandlord(Tenant $tenant, array $blueprint, int $tenantIndex): User
    {
        $username = $blueprint['username'] ?? 'demo-landlord-' . ($tenantIndex + 1);
        $email = $blueprint['email'] ?? 'landlord' . ($tenantIndex + 1) . '@demo.smartroom.local';
        return User::updateOrCreate(
            ['username' => $username],
            [
                'tenant_id' => $tenant->id,
                'role_id' => $this->roles['landlord']->id,
                'name' => $blueprint['owner_name'],
                'phone' => $blueprint['phone'] ?? ('0888000' . str_pad((string) ($tenantIndex + 1), 3, '0', STR_PAD_LEFT)),
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'admin',
                'like' => 'Chủ trọ demo',
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
        $residents = [];
        $occupiedRooms = collect($rooms)
            ->filter(fn (Room $room) => in_array($room->status, ['occupied', 'overdue'], true))
            ->values();

        foreach ($occupiedRooms as $index => $room) {
            $seedNo = (($tenantIndex + 1) * 100) + $index + 1;
            $name = $this->residentNames()[$index % count($this->residentNames())];
            $email = 'resident' . $seedNo . '@demo.smartroom.local';

            $user = User::updateOrCreate(
                ['username' => 'demo-resident-' . $seedNo],
                [
                    'tenant_id' => $tenant->id,
                    'role_id' => $this->roles['resident']->id,
                    'name' => $name,
                    'phone' => '0777' . str_pad((string) $seedNo, 6, '0', STR_PAD_LEFT),
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'like' => 'Khách thuê demo',
                ]
            );

            $resident = Resident::updateOrCreate(
                ['email' => $email],
                [
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'user_id' => $user->id,
                    'name' => $name,
                    'dob' => Carbon::create(1992 + ($index % 10), ($index % 12) + 1, 10)->toDateString(),
                    'phone' => $user->phone,
                    'cccd' => '001' . str_pad((string) $seedNo, 9, '0', STR_PAD_LEFT),
                    'hometown' => ['Hà Nội', 'Nam Định', 'Thái Bình', 'Bắc Ninh', 'Đà Nẵng'][$index % 5],
                    'start_date' => Carbon::today()->subMonths(12 - ($index % 6))->toDateString(),
                    'status' => 'active',
                    'temporary_residence_status' => ['registered', 'none', 'absent'][$index % 3],
                    'version' => 1,
                ]
            );

            if ($index % 3 === 0) {
                ResidentRelative::updateOrCreate(
                    ['resident_id' => $resident->id, 'name' => 'Người thân ' . $room->room_number],
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

            $residents[$room->id] = $resident;
        }

        return $residents;
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
            ['code' => 'AC', 'name' => 'Dieu hoa 9000 BTU', 'unit' => 'cai', 'quantity' => 120],
            ['code' => 'WM', 'name' => 'May giat mini', 'unit' => 'cai', 'quantity' => 50],
            ['code' => 'FR', 'name' => 'Tu lanh 90L', 'unit' => 'cai', 'quantity' => 60],
            ['code' => 'BED', 'name' => 'Giuong sat 1m2', 'unit' => 'cai', 'quantity' => 120],
            ['code' => 'LOCK', 'name' => 'Khoa van tay', 'unit' => 'cai', 'quantity' => 120],
            ['code' => 'CAM', 'name' => 'Camera hanh lang', 'unit' => 'cai', 'quantity' => 30],
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
                'url' => '/demo/full-seeder',
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
                'role' => 'user',
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

        return collect(['101', '102', '201', '202', '301', '302', '401', '402', '501', '502', '601', '602'])
            ->map(function (string $roomNumber, int $index) use ($basePrice, $statuses) {
                return [
                    'room_number' => $roomNumber,
                    'floor' => (int) substr($roomNumber, 0, 1),
                    'status' => $statuses[$index],
                    'room_type' => $index % 3 === 0 ? 'vip' : 'normal',
                    'price' => $basePrice + ($index * 100000),
                    'area' => 22 + ($index * 2),
                    'amenities' => ['dieu hoa', 'nong lanh', 'wifi', $index % 2 === 0 ? 'ban cong' : 'tu lanh'],
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
}
