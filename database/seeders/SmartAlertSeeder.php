<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Contract;
use App\Models\ElectricWaterLog;
use App\Models\Equipment;
use App\Models\Resident;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmartAlertSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::whereHas('rooms')->orderBy('id')->first();
        if (!$tenant) {
            return;
        }

        $occupiedRoom = Room::where('tenant_id', $tenant->id)
            ->whereIn('status', ['occupied', 'overdue'])
            ->orderBy('room_number')
            ->first();

        $overdueRoom = Room::where('tenant_id', $tenant->id)
            ->whereIn('status', ['occupied', 'overdue'])
            ->orderByDesc('room_number')
            ->first();

        $emptyRooms = Room::where('tenant_id', $tenant->id)
            ->where('status', 'empty')
            ->orderBy('room_number')
            ->take(2)
            ->get();

        if (!$occupiedRoom || !$overdueRoom) {
            return;
        }

        $resident = $this->activeResidentFor($tenant->id, $occupiedRoom);
        $overdueResident = $this->activeResidentFor($tenant->id, $overdueRoom);

        $this->seedExpiringContract($tenant->id, $occupiedRoom, $resident);
        $this->seedOverdueBill($tenant->id, $overdueRoom);
        $this->seedOverdueUtilityRecord($tenant->id, $overdueRoom);
        $this->seedLongEmptyRooms($emptyRooms);
        $this->seedLowStockEquipment($tenant->id);
        $this->seedBrokenEquipmentTicket($tenant->id, $overdueRoom, $overdueResident);
    }

    private function activeResidentFor(int $tenantId, Room $room): Resident
    {
        $resident = Resident::where('room_id', $room->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->first();

        if ($resident) {
            return $resident;
        }

        return Resident::firstOrCreate(
            [
                'room_id' => $room->id,
                'email' => 'canhbao.phong' . $room->room_number . '@smartroom.local',
            ],
            [
                'tenant_id' => $tenantId,
                'name' => 'Khach thue phong ' . $room->room_number,
                'phone' => '090' . str_pad((string) $room->id, 7, '0', STR_PAD_LEFT),
                'cccd' => 'CB' . str_pad((string) $room->id, 10, '0', STR_PAD_LEFT),
                'hometown' => 'Ha Noi',
                'start_date' => Carbon::today()->subMonths(10)->toDateString(),
                'status' => 'active',
                'temporary_residence_status' => 'registered',
                'version' => 1,
            ]
        );
    }

    private function seedExpiringContract(int $tenantId, Room $room, Resident $resident): void
    {
        Contract::updateOrCreate(
            ['contract_code' => 'HD-CANHBAO-' . $room->room_number],
            [
                'tenant_id' => $tenantId,
                'room_id' => $room->id,
                'resident_id' => $resident->id,
                'start_date' => Carbon::today()->subMonths(11)->toDateString(),
                'end_date' => Carbon::today()->addDays(15)->toDateString(),
                'deposit' => $room->price,
                'status' => 'active',
                'terms' => 'Hop dong mau dung de hien thi canh bao sap het han tren dashboard.',
                'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=',
            ]
        );
    }

    private function seedOverdueBill(int $tenantId, Room $room): void
    {
        $billingMonth = Carbon::today()->subMonth()->format('Y-m');
        $electricityUsage = 96;
        $waterUsage = 8;
        $electricityCost = $electricityUsage * 3500;
        $waterCost = $waterUsage * 15000;
        $serviceCost = 150000;

        $log = ElectricWaterLog::updateOrCreate(
            [
                'room_id' => $room->id,
                'billing_month' => $billingMonth,
            ],
            [
                'tenant_id' => $tenantId,
                'old_electricity' => 1200,
                'new_electricity' => 1200 + $electricityUsage,
                'old_water' => 160,
                'new_water' => 160 + $waterUsage,
                'electricity_price' => 3500,
                'water_price' => 15000,
            ]
        );

        Bill::updateOrCreate(
            [
                'room_id' => $room->id,
                'billing_month' => $billingMonth,
            ],
            [
                'tenant_id' => $tenantId,
                'electric_water_log_id' => $log->id,
                'room_price' => $room->price,
                'electricity_usage' => $electricityUsage,
                'electricity_cost' => $electricityCost,
                'water_usage' => $waterUsage,
                'water_cost' => $waterCost,
                'service_cost' => $serviceCost,
                'total_amount' => $room->price + $electricityCost + $waterCost + $serviceCost,
                'status' => 'overdue',
                'payment_date' => null,
                'vietqr_url' => null,
            ]
        );

        $room->update(['status' => 'overdue']);
    }

    private function seedOverdueUtilityRecord(int $tenantId, Room $room): void
    {
        UtilityRecord::updateOrCreate(
            [
                'room_id' => $room->id,
                'billing_month' => Carbon::today()->subMonth()->format('Y-m'),
            ],
            [
                'tenant_id' => $tenantId,
                'old_electricity' => 1200,
                'new_electricity' => 1296,
                'old_water' => 160,
                'new_water' => 168,
                'electricity_price' => 3500,
                'water_price' => 15000,
                'status' => 'overdue',
            ]
        );
    }

    private function seedLongEmptyRooms($rooms): void
    {
        foreach ($rooms as $index => $room) {
            DB::table('rooms')
                ->where('id', $room->id)
                ->update([
                    'updated_at' => Carbon::today()->subDays(45 + ($index * 10)),
                ]);
        }
    }

    private function seedLowStockEquipment(int $tenantId): void
    {
        $equipment = Equipment::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->first();

        if (!$equipment) {
            return;
        }

        $equipment->update([
            'total_quantity' => max(1, (int) $equipment->allocated_quantity + 1),
            'description' => 'Du lieu mau: thiet bi sap thieu, can nhap bo sung.',
            'version' => $equipment->version + 1,
        ]);
    }

    private function seedBrokenEquipmentTicket(int $tenantId, Room $room, Resident $resident): void
    {
        Ticket::updateOrCreate(
            [
                'room_id' => $room->id,
                'title' => 'Dieu hoa phong ' . $room->room_number . ' dang hong',
            ],
            [
                'tenant_id' => $tenantId,
                'resident_id' => $resident->id,
                'description' => 'Dieu hoa khong mat, can kiem tra va sua chua som.',
                'category' => 'dien',
                'status' => 'pending',
                'assigned_to' => null,
            ]
        );
    }
}
