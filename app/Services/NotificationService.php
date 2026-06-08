<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\NotificationLog;
use App\Models\RoomEquipment;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotificationService
{
    private const SERVICE_FEE = 150000;

    private const DEFAULT_CHANNELS = ['email', 'zalo', 'sms'];

    public function sendPaymentReminders(int $tenantId, ?string $billingMonth = null, array $channels = self::DEFAULT_CHANNELS): Collection
    {
        $billingMonth ??= now()->format('Y-m');

        return UtilityRecord::with(['room.residents' => fn ($query) => $query->where('status', 'active')])
            ->where('billing_month', $billingMonth)
            ->where('status', '!=', 'paid')
            ->whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
            ->get()
            ->flatMap(function (UtilityRecord $record) use ($tenantId, $billingMonth, $channels) {
                $room = $record->room;
                $resident = $room?->residents->first();

                if (!$room || !$resident) {
                    return collect();
                }

                $total = $this->utilityTotal($record);
                $subject = 'Nhac thanh toan hoa don phong ' . $room->room_number;
                $message = 'Phong ' . $room->room_number . ' co hoa don thang ' . $billingMonth
                    . ' chua thanh toan. Tong tien: ' . number_format($total) . ' VND. Vui long thanh toan truoc ngay 10.';

                if ($record->status !== 'overdue') {
                    $record->update(['status' => 'sent']);
                }

                return $this->sendToChannels($tenantId, 'payment_reminder', $channels, [
                    'name' => $resident->name,
                    'email' => $resident->email,
                    'phone' => $resident->phone,
                ], $subject, $message, UtilityRecord::class, $record->id, [
                    'room_number' => $room->room_number,
                    'billing_month' => $billingMonth,
                    'total_amount' => $total,
                    'simulated' => false,
                ]);
            })
            ->values();
    }

    public function sendContractExpiryReminders(int $tenantId, int $days = 30, array $channels = self::DEFAULT_CHANNELS): Collection
    {
        $today = Carbon::today();
        $limitDate = $today->copy()->addDays($days);

        return Contract::with(['room', 'resident'])
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $limitDate)
            ->orderBy('end_date')
            ->get()
            ->flatMap(function (Contract $contract) use ($tenantId, $today, $channels) {
                $resident = $contract->resident;
                if (!$resident) {
                    return collect();
                }

                $endDate = Carbon::parse($contract->end_date);
                $subject = 'Nhac hop dong sap het han ' . $contract->contract_code;
                $message = 'Hop dong ' . $contract->contract_code . ' cua phong '
                    . ($contract->room->room_number ?? 'N/A') . ' se het han ngay '
                    . $endDate->format('d/m/Y') . ' (con ' . $today->diffInDays($endDate) . ' ngay).';

                return $this->sendToChannels($tenantId, 'contract_expiry', $channels, [
                    'name' => $resident->name,
                    'email' => $resident->email,
                    'phone' => $resident->phone,
                ], $subject, $message, Contract::class, $contract->id, [
                    'contract_code' => $contract->contract_code,
                    'end_date' => $endDate->toDateString(),
                    'simulated' => false,
                ]);
            })
            ->values();
    }

    public function sendMaintenanceReminders(int $tenantId, int $daysSinceAllocated = 90, array $channels = ['email']): Collection
    {
        $cutoff = now()->subDays($daysSinceAllocated);

        return RoomEquipment::with(['room', 'equipment'])
            ->where('tenant_id', $tenantId)
            ->where('quantity', '>', 0)
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('last_allocated_at')
                    ->orWhere('last_allocated_at', '<=', $cutoff);
            })
            ->orderBy('last_allocated_at')
            ->get()
            ->flatMap(function (RoomEquipment $allocation) use ($tenantId, $channels, $daysSinceAllocated) {
                $subject = 'Nhac bao tri thiet bi phong ' . ($allocation->room->room_number ?? 'N/A');
                $message = 'Thiet bi ' . ($allocation->equipment->name ?? 'N/A') . ' tai phong '
                    . ($allocation->room->room_number ?? 'N/A') . ' can kiem tra bao tri dinh ky sau '
                    . $daysSinceAllocated . ' ngay su dung.';

                return $this->sendToChannels($tenantId, 'maintenance_reminder', $channels, [
                    'name' => 'Ban quan ly',
                    'email' => config('mail.from.address'),
                    'phone' => null,
                ], $subject, $message, RoomEquipment::class, $allocation->id, [
                    'room_number' => $allocation->room->room_number ?? null,
                    'equipment_name' => $allocation->equipment->name ?? null,
                    'quantity' => $allocation->quantity,
                    'simulated' => false,
                ]);
            })
            ->values();
    }

    private function sendToChannels(
        int $tenantId,
        string $type,
        array $channels,
        array $recipient,
        string $subject,
        string $message,
        string $targetType,
        int $targetId,
        array $meta = []
    ): Collection {
        return collect($channels)
            ->map(fn ($channel) => $this->send($tenantId, $type, $channel, $recipient, $subject, $message, $targetType, $targetId, $meta));
    }

    private function send(
        int $tenantId,
        string $type,
        string $channel,
        array $recipient,
        string $subject,
        string $message,
        string $targetType,
        int $targetId,
        array $meta
    ): NotificationLog {
        $contact = $channel === 'email'
            ? ($recipient['email'] ?? null)
            : ($recipient['phone'] ?? null);
        $status = $contact ? 'sent' : 'skipped';
        $error = null;

        if ($channel === 'email' && $contact) {
            try {
                Mail::raw($message, function ($mail) use ($contact, $recipient, $subject) {
                    $mail->to($contact, $recipient['name'] ?? null)
                        ->subject($subject);
                });
            } catch (Throwable $exception) {
                $status = 'failed';
                $error = $exception->getMessage();
            }
        }

        Log::info('Notification dispatched', [
            'type' => $type,
            'channel' => $channel,
            'recipient' => $contact,
            'status' => $status,
            'subject' => $subject,
            'error' => $error,
        ]);

        return NotificationLog::create([
            'tenant_id' => $tenantId,
            'type' => $type,
            'channel' => $channel,
            'recipient_name' => $recipient['name'] ?? null,
            'recipient_contact' => $contact,
            'subject' => $subject,
            'message' => $message,
            'status' => $status,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'meta' => array_merge($meta, [
                'real_email' => $channel === 'email',
                'simulated' => $channel !== 'email',
                'error' => $error,
            ]),
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }

    private function utilityTotal(UtilityRecord $record): int
    {
        $electricityUsage = max(0, (int) $record->new_electricity - (int) $record->old_electricity);
        $waterUsage = max(0, (int) $record->new_water - (int) $record->old_water);
        $roomAmount = (int) optional($record->room)->price;

        return $roomAmount
            + ($electricityUsage * (int) $record->electricity_price)
            + ($waterUsage * (int) $record->water_price)
            + self::SERVICE_FEE;
    }
}
