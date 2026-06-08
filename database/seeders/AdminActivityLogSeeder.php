<?php

namespace Database\Seeders;

use App\Models\AdminActivityLog;
use App\Models\Contract;
use App\Models\Equipment;
use App\Models\Resident;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdminActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::whereHas('rooms')->orderBy('id')->first();
        if (!$tenant) {
            return;
        }

        $user = User::where('tenant_id', $tenant->id)
            ->where('name', 'like', '%Chủ Nhà%')
            ->first()
            ?? User::where('tenant_id', $tenant->id)->first();

        AdminActivityLog::where('tenant_id', $tenant->id)
            ->where('metadata->seeded', true)
            ->delete();

        $rooms = Room::where('tenant_id', $tenant->id)
            ->orderBy('room_number')
            ->take(5)
            ->get()
            ->keyBy('room_number');

        if ($rooms->isEmpty()) {
            return;
        }

        $this->log(
            $tenant->id,
            $user,
            'create',
            'residents',
            'Cư dân Trần Thanh Hùng bắt đầu thuê phòng 101',
            Resident::where('room_id', $rooms->get('101')?->id)->first(),
            Carbon::now()->subDays(9)->setTime(9, 15),
            ['room_number' => '101', 'resident_name' => 'Trần Thanh Hùng']
        );

        $record101 = $this->utilityRecordFor($rooms->get('101'));
        $this->log(
            $tenant->id,
            $user,
            'create',
            'utilities',
            'Chốt điện nước tháng này cho phòng 101',
            $record101,
            Carbon::now()->subDays(5)->setTime(17, 30),
            ['room_number' => '101', 'billing_month' => $record101?->billing_month]
        );

        $record103 = $this->utilityRecordFor($rooms->get('103'));
        $this->log(
            $tenant->id,
            $user,
            'notify',
            'utilities',
            'Gửi nhắc thanh toán hóa đơn quá hạn cho phòng 103',
            $record103,
            Carbon::now()->subDays(4)->setTime(8, 45),
            ['room_number' => '103', 'billing_month' => $record103?->billing_month]
        );

        $this->log(
            $tenant->id,
            $user,
            'payment',
            'payments',
            'Phòng 102 đã thanh toán tiền phòng và điện nước',
            $this->utilityRecordFor($rooms->get('102')),
            Carbon::now()->subDays(3)->setTime(20, 10),
            ['room_number' => '102', 'payment_method' => 'vietqr']
        );

        $contract = Contract::where('tenant_id', $tenant->id)->with('room')->orderByDesc('end_date')->first();
        $this->log(
            $tenant->id,
            $user,
            'update',
            'contracts',
            'Kiểm tra hợp đồng sắp hết hạn của phòng ' . ($contract->room->room_number ?? 'N/A'),
            $contract,
            Carbon::now()->subDays(2)->setTime(14, 5),
            ['room_number' => $contract->room->room_number ?? null, 'end_date' => $contract->end_date ?? null]
        );

        $equipment = Equipment::where('tenant_id', $tenant->id)->orderBy('name')->first();
        $this->log(
            $tenant->id,
            $user,
            'allocate',
            'equipment',
            'Bàn giao thiết bị cho phòng 201',
            $equipment,
            Carbon::now()->subDay()->setTime(10, 20),
            ['room_number' => '201', 'equipment_name' => $equipment->name ?? null]
        );

        $this->log(
            $tenant->id,
            $user,
            'update',
            'rooms',
            'Chuyển phòng 402 sang trạng thái bảo trì',
            Room::where('tenant_id', $tenant->id)->where('room_number', '402')->first(),
            Carbon::now()->subHours(6),
            ['room_number' => '402', 'status' => 'maintenance']
        );
    }

    private function utilityRecordFor(?Room $room): ?UtilityRecord
    {
        if (!$room) {
            return null;
        }

        return UtilityRecord::where('room_id', $room->id)
            ->orderByDesc('billing_month')
            ->first();
    }

    private function log(
        int $tenantId,
        ?User $user,
        string $action,
        string $module,
        string $description,
        $subject,
        Carbon $createdAt,
        array $metadata = []
    ): void {
        AdminActivityLog::create([
            'tenant_id' => $tenantId,
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Quản lý nhà trọ',
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => '127.0.0.1',
            'method' => 'SEED',
            'url' => null,
            'user_agent' => 'SmartRoom sample data',
            'before_values' => null,
            'after_values' => null,
            'metadata' => array_merge(['seeded' => true], $metadata),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}
