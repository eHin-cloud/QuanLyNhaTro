<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Room;
use App\Models\RoomEquipment;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::whereHas('rooms')->orderBy('id')->first() ?? Tenant::orderBy('id')->first();
        if (!$tenant) {
            return;
        }

        $items = [
            ['code' => 'TB-001', 'name' => 'Dieu hoa 9000 BTU', 'unit' => 'cai', 'quantity' => 14],
            ['code' => 'TB-002', 'name' => 'Binh nong lanh 20L', 'unit' => 'cai', 'quantity' => 16],
            ['code' => 'TB-003', 'name' => 'May giat mini', 'unit' => 'cai', 'quantity' => 6],
            ['code' => 'TB-004', 'name' => 'Tu lanh 90L', 'unit' => 'cai', 'quantity' => 10],
            ['code' => 'TB-005', 'name' => 'Giuong sat 1m2', 'unit' => 'cai', 'quantity' => 18],
            ['code' => 'TB-006', 'name' => 'Nem cao su', 'unit' => 'cai', 'quantity' => 18],
            ['code' => 'TB-007', 'name' => 'Tu quan ao 2 canh', 'unit' => 'cai', 'quantity' => 12],
            ['code' => 'TB-008', 'name' => 'Ban hoc go', 'unit' => 'cai', 'quantity' => 12],
            ['code' => 'TB-009', 'name' => 'Ghe nhua cao cap', 'unit' => 'cai', 'quantity' => 24],
            ['code' => 'TB-010', 'name' => 'Quat tran', 'unit' => 'cai', 'quantity' => 12],
            ['code' => 'TB-011', 'name' => 'Den led tran', 'unit' => 'bo', 'quantity' => 30],
            ['code' => 'TB-012', 'name' => 'Router wifi', 'unit' => 'cai', 'quantity' => 8],
            ['code' => 'TB-013', 'name' => 'Camera hanh lang', 'unit' => 'cai', 'quantity' => 10],
            ['code' => 'TB-014', 'name' => 'Khoa van tay', 'unit' => 'cai', 'quantity' => 12],
            ['code' => 'TB-015', 'name' => 'Bep dien don', 'unit' => 'cai', 'quantity' => 9],
            ['code' => 'TB-016', 'name' => 'Chau rua inox', 'unit' => 'cai', 'quantity' => 12],
            ['code' => 'TB-017', 'name' => 'Guong nha tam', 'unit' => 'cai', 'quantity' => 12],
            ['code' => 'TB-018', 'name' => 'Voi sen tam', 'unit' => 'bo', 'quantity' => 12],
            ['code' => 'TB-019', 'name' => 'Ke bep treo tuong', 'unit' => 'cai', 'quantity' => 10],
            ['code' => 'TB-020', 'name' => 'Rem cua chong nang', 'unit' => 'bo', 'quantity' => 12],
        ];

        $equipmentByCode = [];
        foreach ($items as $item) {
            $equipment = Equipment::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $item['code']],
                [
                    'name' => $item['name'],
                    'unit' => $item['unit'],
                    'total_quantity' => $item['quantity'],
                    'allocated_quantity' => 0,
                    'description' => 'Du lieu mau phuc vu quan ly thiet bi phong tro.',
                    'version' => 1,
                ]
            );

            $equipmentByCode[$item['code']] = $equipment;
        }

        RoomEquipment::where('tenant_id', $tenant->id)
            ->whereIn('equipment_id', collect($equipmentByCode)->pluck('id'))
            ->delete();

        $rooms = Room::where('tenant_id', $tenant->id)
            ->orderBy('room_number')
            ->take(12)
            ->get()
            ->values();

        if ($rooms->isEmpty()) {
            return;
        }

        $standardRoomItems = [
            'TB-001' => 1,
            'TB-002' => 1,
            'TB-005' => 1,
            'TB-006' => 1,
            'TB-007' => 1,
            'TB-008' => 1,
            'TB-009' => 2,
            'TB-010' => 1,
            'TB-011' => 2,
            'TB-014' => 1,
            'TB-016' => 1,
            'TB-017' => 1,
            'TB-018' => 1,
        ];

        foreach ($rooms as $index => $room) {
            foreach ($standardRoomItems as $code => $quantity) {
                $this->allocate($tenant->id, $room->id, $equipmentByCode[$code], $quantity);
            }

            if ($index % 2 === 0) {
                $this->allocate($tenant->id, $room->id, $equipmentByCode['TB-004'], 1);
                $this->allocate($tenant->id, $room->id, $equipmentByCode['TB-020'], 1);
            }

            if ($index % 3 === 0) {
                $this->allocate($tenant->id, $room->id, $equipmentByCode['TB-003'], 1);
                $this->allocate($tenant->id, $room->id, $equipmentByCode['TB-015'], 1);
            }
        }

        foreach ($equipmentByCode as $equipment) {
            $allocated = RoomEquipment::where('equipment_id', $equipment->id)->sum('quantity');
            $equipment->update([
                'allocated_quantity' => $allocated,
                'version' => $equipment->version + 1,
            ]);
        }
    }

    private function allocate(int $tenantId, int $roomId, Equipment $equipment, int $quantity): void
    {
        RoomEquipment::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'room_id' => $roomId,
                'equipment_id' => $equipment->id,
            ],
            [
                'quantity' => $quantity,
                'last_allocated_at' => now(),
            ]
        );
    }
}
