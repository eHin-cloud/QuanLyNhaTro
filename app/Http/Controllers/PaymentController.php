<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Tenant;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    private const SERVICE_FEE = 150000;

    private const PAYMENT_METHODS = [
        'cash' => 'Tiền mặt',
        'bank_transfer' => 'Chuyển khoản',
        'vietqr' => 'VietQR',
        'other' => 'Khác',
    ];

    public function index(Request $request)
    {
        $tenantId = $this->currentTenantId();
        $filters = $this->filters($request);

        $records = $this->filteredRecordQuery($tenantId, $filters)
            ->orderByDesc('billing_month')
            ->orderBy('room_id')
            ->get()
            ->map(fn (UtilityRecord $record) => $this->decorateRecord($record));

        if ($filters['status'] === 'draft') {
            $records = collect();
        }

        $draftRooms = $this->draftRooms($tenantId, $filters);
        $paymentRows = $records->concat($draftRooms)
            ->sortBy([
                ['billing_month', 'desc'],
                ['room_number', 'asc'],
            ])
            ->values();

        $summary = [
            'draft_count' => $paymentRows->where('status', 'draft')->count(),
            'sent_count' => $paymentRows->where('status', 'sent')->count(),
            'paid_count' => $paymentRows->where('status', 'paid')->count(),
            'overdue_count' => $paymentRows->where('status', 'overdue')->count(),
            'paid_total' => $records->where('status', 'paid')->sum('total_amount'),
            'unpaid_total' => $records->whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
        ];

        $rooms = Room::where('tenant_id', $tenantId)
            ->orderBy('room_number')
            ->get(['id', 'room_number']);

        $histories = UtilityRecord::with(['room.building'])
            ->whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
            ->when($filters['room_id'], fn ($query) => $query->where('room_id', $filters['room_id']))
            ->orderBy('room_id')
            ->orderByDesc('billing_month')
            ->get()
            ->map(fn (UtilityRecord $record) => $this->decorateRecord($record))
            ->groupBy('room_id');

        $revenueReports = $this->revenueReports($tenantId, $filters);

        return view('admin.payments.index', [
            'filters' => $filters,
            'paymentRows' => $paymentRows,
            'summary' => $summary,
            'rooms' => $rooms,
            'histories' => $histories,
            'revenueReports' => $revenueReports,
            'paymentMethods' => self::PAYMENT_METHODS,
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    public function update(Request $request, UtilityRecord $payment)
    {
        $this->authorizeTenantRecord($payment);

        $validated = $request->validate([
            'status' => 'required|in:sent,paid,overdue',
            'payment_date' => 'nullable|required_if:status,paid|date',
            'payment_method' => 'nullable|required_if:status,paid|in:cash,bank_transfer,vietqr,other',
        ]);

        $payment->update([
            'status' => $validated['status'],
            'payment_date' => $validated['status'] === 'paid'
                ? Carbon::parse($validated['payment_date'] ?? now())->startOfDay()
                : null,
            'payment_method' => $validated['status'] === 'paid'
                ? $validated['payment_method']
                : null,
        ]);

        $this->syncRoomPaymentStatus($payment->room_id);

        return back()->with('success', 'Đã cập nhật trạng thái thanh toán.');
    }

    public function export(Request $request): StreamedResponse
    {
        $tenantId = $this->currentTenantId();
        $filters = $this->filters($request);
        $rows = $this->revenueReports($tenantId, $filters);
        $fileName = 'bao-cao-doanh-thu-' . $filters['report_period'] . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows, $filters) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Ky bao cao', 'So hoa don da thanh toan', 'Doanh thu']);

            foreach ($rows as $row) {
                fputcsv($handle, [$row['label'], $row['paid_count'], $row['total_revenue']]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Loai bao cao', $filters['report_period']]);
            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filters(Request $request): array
    {
        return [
            'billing_month' => $this->normalizeMonth($request->query('billing_month')) ?? now()->format('Y-m'),
            'from_month' => $this->normalizeMonth($request->query('from_month')),
            'to_month' => $this->normalizeMonth($request->query('to_month')),
            'status' => in_array($request->query('status'), ['draft', 'sent', 'paid', 'overdue'], true) ? $request->query('status') : null,
            'payment_method' => array_key_exists($request->query('payment_method'), self::PAYMENT_METHODS) ? $request->query('payment_method') : null,
            'room_id' => $request->filled('room_id') ? (int) $request->query('room_id') : null,
            'q' => trim((string) $request->query('q', '')),
            'report_period' => in_array($request->query('report_period'), ['month', 'quarter', 'year'], true) ? $request->query('report_period') : 'month',
        ];
    }

    private function filteredRecordQuery(int $tenantId, array $filters)
    {
        return UtilityRecord::with(['room.building', 'room.residents' => fn ($query) => $query->where('status', 'active')])
            ->whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
            ->when($filters['from_month'], fn ($query) => $query->where('billing_month', '>=', $filters['from_month']))
            ->when($filters['to_month'], fn ($query) => $query->where('billing_month', '<=', $filters['to_month']))
            ->when(!$filters['from_month'] && !$filters['to_month'], fn ($query) => $query->where('billing_month', $filters['billing_month']))
            ->when($filters['status'] && $filters['status'] !== 'draft', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['payment_method'], fn ($query) => $query->where('payment_method', $filters['payment_method']))
            ->when($filters['room_id'], fn ($query) => $query->where('room_id', $filters['room_id']))
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $identityKeyword = $this->prefixLike($filters['q']);
                $nameKeyword = $this->containsLike($filters['q']);

                $query->whereHas('room', function ($roomQuery) use ($identityKeyword, $nameKeyword) {
                    $roomQuery->where('room_number', 'like', $identityKeyword)
                        ->orWhereHas('residents', function ($residentQuery) use ($identityKeyword, $nameKeyword) {
                            $residentQuery->where('name', 'like', $nameKeyword)
                                ->orWhere('phone', 'like', $identityKeyword)
                                ->orWhere('cccd', 'like', $identityKeyword);
                        });
                });
            });
    }

    private function draftRooms(int $tenantId, array $filters): Collection
    {
        if ($filters['payment_method'] || ($filters['status'] && $filters['status'] !== 'draft')) {
            return collect();
        }

        return Room::with(['building', 'residents' => fn ($query) => $query->where('status', 'active')])
            ->where('tenant_id', $tenantId)
            ->where('status', '!=', 'empty')
            ->whereDoesntHave('utilityRecords', fn ($query) => $query->where('billing_month', $filters['billing_month']))
            ->when($filters['room_id'], fn ($query) => $query->where('id', $filters['room_id']))
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $identityKeyword = $this->prefixLike($filters['q']);
                $nameKeyword = $this->containsLike($filters['q']);

                $query->where(function ($roomQuery) use ($identityKeyword, $nameKeyword) {
                    $roomQuery->where('room_number', 'like', $identityKeyword)
                        ->orWhereHas('residents', function ($residentQuery) use ($identityKeyword, $nameKeyword) {
                            $residentQuery->where('name', 'like', $nameKeyword)
                                ->orWhere('phone', 'like', $identityKeyword)
                                ->orWhere('cccd', 'like', $identityKeyword);
                        });
                });
            })
            ->orderBy('room_number')
            ->get()
            ->map(function (Room $room) use ($filters) {
                return (object) [
                    'id' => null,
                    'room_id' => $room->id,
                    'room_number' => $room->room_number,
                    'building_name' => $room->building->name ?? 'Chưa có tòa nhà',
                    'resident_name' => optional($room->residents->first())->name ?? 'Chưa gán cư dân',
                    'billing_month' => $filters['billing_month'],
                    'status' => 'draft',
                    'status_label' => 'Chưa gửi',
                    'status_class' => 'bg-slate-500/10 text-slate-300 border-slate-500/20',
                    'room_amount' => (int) $room->price,
                    'electricity_usage' => 0,
                    'electricity_amount' => 0,
                    'water_usage' => 0,
                    'water_amount' => 0,
                    'service_amount' => self::SERVICE_FEE,
                    'total_amount' => (int) $room->price + self::SERVICE_FEE,
                    'payment_date' => null,
                    'payment_method' => null,
                ];
            });
    }

    private function decorateRecord(UtilityRecord $record): object
    {
        $electricityUsage = max(0, (int) $record->new_electricity - (int) $record->old_electricity);
        $waterUsage = max(0, (int) $record->new_water - (int) $record->old_water);
        $roomAmount = (int) optional($record->room)->price;
        $electricityAmount = $electricityUsage * (int) $record->electricity_price;
        $waterAmount = $waterUsage * (int) $record->water_price;
        $labels = $this->statusLabels();

        return (object) [
            'id' => $record->id,
            'room_id' => $record->room_id,
            'room_number' => $record->room->room_number ?? 'N/A',
            'building_name' => $record->room->building->name ?? 'Chưa có tòa nhà',
            'resident_name' => optional($record->room->residents->first())->name ?? 'Chưa gán cư dân',
            'billing_month' => $record->billing_month,
            'status' => $record->status,
            'status_label' => $labels[$record->status]['label'] ?? $record->status,
            'status_class' => $labels[$record->status]['class'] ?? 'bg-slate-500/10 text-slate-300 border-slate-500/20',
            'room_amount' => $roomAmount,
            'electricity_usage' => $electricityUsage,
            'electricity_amount' => $electricityAmount,
            'water_usage' => $waterUsage,
            'water_amount' => $waterAmount,
            'service_amount' => self::SERVICE_FEE,
            'total_amount' => $roomAmount + $electricityAmount + $waterAmount + self::SERVICE_FEE,
            'payment_date' => $record->payment_date,
            'payment_method' => $record->payment_method,
        ];
    }

    private function revenueReports(int $tenantId, array $filters): Collection
    {
        return UtilityRecord::with('room')
            ->whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
            ->where('status', 'paid')
            ->when($filters['from_month'], fn ($query) => $query->where('billing_month', '>=', $filters['from_month']))
            ->when($filters['to_month'], fn ($query) => $query->where('billing_month', '<=', $filters['to_month']))
            ->when($filters['room_id'], fn ($query) => $query->where('room_id', $filters['room_id']))
            ->when($filters['payment_method'], fn ($query) => $query->where('payment_method', $filters['payment_method']))
            ->orderBy('billing_month')
            ->get()
            ->map(fn (UtilityRecord $record) => $this->decorateRecord($record))
            ->groupBy(fn ($record) => $this->periodKey($record->billing_month, $filters['report_period']))
            ->map(function (Collection $items, string $key) {
                return [
                    'key' => $key,
                    'label' => $this->periodLabel($key),
                    'paid_count' => $items->count(),
                    'total_revenue' => $items->sum('total_amount'),
                ];
            })
            ->sortBy('key')
            ->values();
    }

    private function periodKey(string $billingMonth, string $period): string
    {
        [$year, $month] = explode('-', $billingMonth);

        return match ($period) {
            'quarter' => $year . '-Q' . (int) ceil(((int) $month) / 3),
            'year' => $year,
            default => $billingMonth,
        };
    }

    private function periodLabel(string $key): string
    {
        if (preg_match('/^(\d{4})-(\d{2})$/', $key, $matches)) {
            return 'Tháng ' . (int) $matches[2] . '/' . $matches[1];
        }

        if (preg_match('/^(\d{4})-Q([1-4])$/', $key, $matches)) {
            return 'Quý ' . $matches[2] . '/' . $matches[1];
        }

        return 'Năm ' . $key;
    }

    private function statusLabels(): array
    {
        return [
            'draft' => ['label' => 'Chưa gửi', 'class' => 'bg-slate-500/10 text-slate-300 border-slate-500/20'],
            'sent' => ['label' => 'Đã gửi', 'class' => 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20'],
            'paid' => ['label' => 'Đã thanh toán', 'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20'],
            'overdue' => ['label' => 'Quá hạn', 'class' => 'bg-rose-500/10 text-rose-300 border-rose-500/20'],
        ];
    }

    private function normalizeMonth($value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = trim((string) $value);
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)) {
            abort(404, 'Tháng không hợp lệ.');
        }

        return $value;
    }

    private function currentTenantId(): int
    {
        $tenantId = Auth::user()?->tenant_id;

        if ($tenantId) {
            return (int) $tenantId;
        }

        $fallbackTenantId = Tenant::query()->value('id');
        if (!$fallbackTenantId) {
            abort(404, 'Khong tim thay tenant de hien thi thanh toan.');
        }

        return (int) $fallbackTenantId;
    }

    private function prefixLike(string $value): string
    {
        return $this->escapeLike($value) . '%';
    }

    private function containsLike(string $value): string
    {
        return '%' . $this->escapeLike($value) . '%';
    }

    private function escapeLike(string $value): string
    {
        return addcslashes(trim($value), '\%_');
    }

    private function authorizeTenantRecord(UtilityRecord $record): void
    {
        $record->loadMissing('room');

        if (!$record->room || $record->room->tenant_id !== $this->currentTenantId()) {
            abort(404);
        }
    }

    private function syncRoomPaymentStatus(int $roomId): void
    {
        $room = Room::find($roomId);

        if (!$room || $room->status === 'empty' || $room->status === 'maintenance') {
            return;
        }

        $hasUnpaid = UtilityRecord::where('room_id', $roomId)
            ->where('status', '!=', 'paid')
            ->exists();

        $room->update(['status' => $hasUnpaid ? 'overdue' : 'occupied']);
    }
}
