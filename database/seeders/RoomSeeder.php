<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::orderBy('id')->first();

        if (!$tenant) {
            $tenant = Tenant::create([
                'name' => 'SmartRoom Demo',
                'email' => 'demo@smartroom.local',
                'phone' => '0900000000',
                'bank_name' => 'MB',
                'bank_account_no' => '9999888889999',
                'bank_account_name' => 'SMARTROOM DEMO',
            ]);
        }

        $building = Building::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'SmartRoom Demo Building',
            ],
            [
                'address' => '12 Ngo 105 Xuan Thuy, Cau Giay, Ha Noi',
                'description' => 'Toa nha mau dung cho du lieu phong.',
            ]
        );

        $rooms = [
            ['101', 1, 'occupied', 'normal', 3200000, 25],
            ['102', 1, 'occupied', 'normal', 3200000, 25],
            ['103', 1, 'overdue', 'vip', 3500000, 28],
            ['104', 1, 'empty', 'normal', 3500000, 28],
            ['201', 2, 'occupied', 'normal', 3300000, 25],
            ['202', 2, 'occupied', 'normal', 3300000, 25],
            ['203', 2, 'overdue', 'vip', 3600000, 28],
            ['204', 2, 'empty', 'normal', 3600000, 28],
            ['301', 3, 'occupied', 'normal', 3500000, 27],
            ['302', 3, 'occupied', 'normal', 3500000, 27],
            ['303', 3, 'overdue', 'vip', 3800000, 30],
            ['304', 3, 'empty', 'normal', 3800000, 30],
            ['401', 4, 'occupied', 'normal', 3700000, 28],
            ['402', 4, 'maintenance', 'normal', 3700000, 28],
            ['403', 4, 'empty', 'vip', 4000000, 32],
            ['404', 4, 'occupied', 'normal', 3900000, 30],
            ['501', 5, 'empty', 'normal', 4100000, 32],
            ['502', 5, 'occupied', 'vip', 4300000, 35],
            ['503', 5, 'maintenance', 'normal', 4100000, 32],
            ['504', 5, 'empty', 'vip', 4500000, 36],
        ];

        foreach ($rooms as [$roomNumber, $floor, $status, $roomType, $price, $area]) {
            Room::updateOrCreate(
                [
                    'building_id' => $building->id,
                    'room_number' => $roomNumber,
                ],
                [
                    'tenant_id' => $tenant->id,
                    'floor' => $floor,
                    'status' => $status,
                    'room_type' => $roomType,
                    'price' => $price,
                    'area' => $area,
                    'amenities' => $this->amenitiesFor($roomType, $floor),
                    'description' => 'Phong mau ' . $roomNumber . ' phuc vu quan ly nha tro.',
                    'version' => 1,
                ]
            );
        }
    }

    private function amenitiesFor(string $roomType, int $floor): array
    {
        $amenities = ['gac lung', 'dieu hoa', 'nuoc nong'];

        if ($roomType === 'vip') {
            $amenities[] = 'tu lanh';
            $amenities[] = 'may giat rieng';
        }

        if ($floor >= 3) {
            $amenities[] = 'ban cong';
        }

        return $amenities;
    }
}
