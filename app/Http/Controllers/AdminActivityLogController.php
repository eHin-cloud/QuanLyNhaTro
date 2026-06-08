<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminActivityLogController extends Controller
{
    private const ACTION_LABELS = [
        'create' => 'Tạo mới',
        'update' => 'Cập nhật',
        'delete' => 'Xóa',
        'payment' => 'Thanh toán',
        'allocate' => 'Bàn giao',
        'recover' => 'Thu hồi',
        'notify' => 'Thông báo',
    ];

    private const MODULE_LABELS = [
        'rooms' => 'Phòng',
        'residents' => 'Cư dân',
        'relatives' => 'Người thân',
        'contracts' => 'Hợp đồng',
        'utilities' => 'Điện nước',
        'payments' => 'Thanh toán',
        'equipment' => 'Thiết bị',
        'contact_requests' => 'Yêu cầu tư vấn',
    ];

    public function index(Request $request)
    {
        $tenantId = $this->currentTenantId();
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'action' => array_key_exists($request->query('action'), self::ACTION_LABELS) ? $request->query('action') : null,
            'module' => array_key_exists($request->query('module'), self::MODULE_LABELS) ? $request->query('module') : null,
            'room_number' => $this->normalizeRoomNumber($request->query('room_number')),
            'from_date' => $this->normalizeDate($request->query('from_date')),
            'to_date' => $this->normalizeDate($request->query('to_date')),
        ];

        $baseQuery = AdminActivityLog::query();
        if ($tenantId && AdminActivityLog::where('tenant_id', $tenantId)->exists()) {
            $baseQuery->where('tenant_id', $tenantId);
        }

        $logs = (clone $baseQuery)
            ->when($filters['action'], fn ($query) => $query->where('action', $filters['action']))
            ->when($filters['module'], fn ($query) => $query->where('module', $filters['module']))
            ->when($filters['room_number'], fn ($query) => $this->filterRoomNumber($query, $filters['room_number']))
            ->when($filters['from_date'], fn ($query) => $query->whereDate('created_at', '>=', $filters['from_date']))
            ->when($filters['to_date'], fn ($query) => $query->whereDate('created_at', '<=', $filters['to_date']))
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $keyword = '%' . addcslashes($filters['q'], '\%_') . '%';

                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('description', 'like', $keyword)
                        ->orWhere('user_name', 'like', $keyword)
                        ->orWhere('ip_address', 'like', $keyword)
                        ->orWhere('metadata', 'like', $keyword);
                });
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'deletes' => (clone $baseQuery)->where('action', 'delete')->count(),
            'updates' => (clone $baseQuery)->where('action', 'update')->count(),
        ];

        $roomOptions = Room::query()
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->orderBy('room_number')
            ->pluck('room_number')
            ->values();

        $moduleSummary = collect(self::MODULE_LABELS)
            ->map(fn ($label, $module) => [
                'module' => $module,
                'label' => $label,
                'count' => (clone $baseQuery)->where('module', $module)->count(),
            ])
            ->filter(fn ($item) => $item['count'] > 0)
            ->values();

        return view('admin.activity_logs.index', [
            'logs' => $logs,
            'filters' => $filters,
            'summary' => $summary,
            'actionLabels' => self::ACTION_LABELS,
            'moduleLabels' => self::MODULE_LABELS,
            'roomOptions' => $roomOptions,
            'moduleSummary' => $moduleSummary,
        ]);
    }

    private function normalizeDate($value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = trim((string) $value);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            abort(404, 'Ngày lọc không hợp lệ.');
        }

        return $value;
    }

    private function normalizeRoomNumber($value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = trim(strip_tags((string) $value));
        if (mb_strlen($value) > 50) {
            abort(404, 'Số phòng không hợp lệ.');
        }

        return $value;
    }

    private function filterRoomNumber($query, string $roomNumber)
    {
        $keyword = '%' . addcslashes($roomNumber, '\%_') . '%';

        return $query->where(function ($subQuery) use ($keyword, $roomNumber) {
            $subQuery->where('description', 'like', $keyword)
                ->orWhere('metadata->room_number', $roomNumber)
                ->orWhere('metadata', 'like', $keyword);
        });
    }

    private function currentTenantId(): ?int
    {
        return Auth::user()?->tenant_id ?? Tenant::query()->value('id');
    }
}
