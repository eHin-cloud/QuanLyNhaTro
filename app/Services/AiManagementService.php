<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Resident;
use App\Models\Room;
use App\Models\Ticket;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiManagementService
{
    private const SERVICE_FEE = 150000;

    public function dashboardInsight(int $tenantId): array
    {
        $context = $this->dashboardContext($tenantId);
        $fallback = $this->fallbackDashboardInsight($context);

        if (!$this->isEnabled()) {
            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_not_configured',
            ]);
        }

        try {
            $content = $this->chatJson([
                [
                    'role' => 'system',
                    'content' => 'Ban la tro ly phan tich van hanh nha tro. Chi tra ve JSON hop le.',
                ],
                [
                    'role' => 'user',
                    'content' => implode("\n", [
                        'Hay nhan xet dashboard/doanh thu bang tieng Viet cho chu tro.',
                        'Chi tra ve JSON dang {"summary":"...","highlights":["..."],"risks":["..."],"suggestions":["..."]}.',
                        'Khong bia so lieu ngoai du lieu duoc cung cap.',
                        'Neu du lieu it, noi ro la du lieu con han che.',
                        '',
                        'Du lieu:',
                        json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]),
                ],
            ]);

            return [
                'summary' => trim((string) ($content['summary'] ?? $fallback['summary'])),
                'highlights' => $this->stringList($content['highlights'] ?? $fallback['highlights']),
                'risks' => $this->stringList($content['risks'] ?? $fallback['risks']),
                'suggestions' => $this->stringList($content['suggestions'] ?? $fallback['suggestions']),
                'used_ai' => true,
                'fallback_reason' => null,
            ];
        } catch (Throwable $exception) {
            Log::warning('AI dashboard insight failed', [
                'tenant_id' => $tenantId,
                'error' => $exception->getMessage(),
            ]);

            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_failed',
            ]);
        }
    }

    public function answerManagementQuestion(int $tenantId, string $question): array
    {
        $question = trim($question);
        $context = $this->assistantContext($tenantId);

        if (!$this->isEnabled()) {
            return [
                'answer' => $this->fallbackAnswer($context, $question),
                'used_ai' => false,
                'fallback_reason' => 'ai_not_configured',
            ];
        }

        try {
            $content = $this->chatJson([
                [
                    'role' => 'system',
                    'content' => implode(' ', [
                        'Ban la tro ly quan ly nha tro noi tieng Viet.',
                        'Chi tra loi dua tren du lieu JSON duoc cung cap.',
                        'Khong duoc truy cap hay suy doan du lieu tenant khac.',
                        'Neu khong du du lieu, hay noi ro khong co du lieu.',
                        'Khong thuc hien them/sua/xoa du lieu.',
                    ]),
                ],
                [
                    'role' => 'user',
                    'content' => implode("\n", [
                        'Cau hoi cua chu tro: ' . $question,
                        '',
                        'Hay tra ve JSON dang {"answer":"..."} bang tieng Viet, ngan gon nhung du thong tin.',
                        '',
                        'Du lieu cua tenant hien tai:',
                        json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]),
                ],
            ]);

            $answer = trim((string) ($content['answer'] ?? ''));
            if ($answer === '') {
                throw new \RuntimeException('AI answer is empty.');
            }

            return [
                'answer' => $answer,
                'used_ai' => true,
                'fallback_reason' => null,
            ];
        } catch (Throwable $exception) {
            Log::warning('AI management assistant failed', [
                'tenant_id' => $tenantId,
                'error' => $exception->getMessage(),
            ]);

            return [
                'answer' => $this->fallbackAnswer($context, $question),
                'used_ai' => false,
                'fallback_reason' => 'ai_failed',
            ];
        }
    }

    public function analyzeMaintenanceTicket(string $description, ?string $title = null): array
    {
        $fallback = $this->fallbackTicketAnalysis($description, $title);

        if (!$this->isEnabled()) {
            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_not_configured',
            ]);
        }

        try {
            $content = $this->chatJson([
                [
                    'role' => 'system',
                    'content' => 'Ban la tro ly xu ly su co bao tri nha tro. Chi tra ve JSON hop le.',
                ],
                [
                    'role' => 'user',
                    'content' => implode("\n", [
                        'Phan tich su co bao tri tu mo ta cua cu dan.',
                        'Chi tra ve JSON dang {"title":"...","category":"electric|water|furniture|maintenance|other","priority":"low|medium|high","suggestion":"...","normalized_description":"..."}.',
                        'Khong bia thong tin. Neu mo ta khong ro, de category la other.',
                        '',
                        'Tieu de hien co: ' . ($title ?: 'Chua co'),
                        'Mo ta: ' . $description,
                    ]),
                ],
            ]);

            $category = (string) ($content['category'] ?? $fallback['category']);
            if (!in_array($category, ['electric', 'water', 'furniture', 'maintenance', 'other'], true)) {
                $category = 'other';
            }

            $priority = (string) ($content['priority'] ?? $fallback['priority']);
            if (!in_array($priority, ['low', 'medium', 'high'], true)) {
                $priority = 'medium';
            }

            return [
                'title' => mb_substr(trim((string) ($content['title'] ?? $fallback['title'])), 0, 150),
                'category' => $category,
                'priority' => $priority,
                'suggestion' => trim((string) ($content['suggestion'] ?? $fallback['suggestion'])),
                'normalized_description' => mb_substr(trim((string) ($content['normalized_description'] ?? $fallback['normalized_description'])), 0, 1000),
                'used_ai' => true,
                'fallback_reason' => null,
            ];
        } catch (Throwable $exception) {
            Log::warning('AI ticket analysis failed', ['error' => $exception->getMessage()]);

            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_failed',
            ]);
        }
    }

    public function generateContractTerms(array $data): array
    {
        $fallback = [
            'terms' => $this->fallbackContractTerms($data),
        ];

        if (!$this->isEnabled()) {
            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_not_configured',
            ]);
        }

        try {
            $content = $this->chatJson([
                [
                    'role' => 'system',
                    'content' => 'Ban la tro ly soan dieu khoan hop dong thue phong tro bang tieng Viet. Chi tra ve JSON hop le.',
                ],
                [
                    'role' => 'user',
                    'content' => implode("\n", [
                        'Soan dieu khoan hop dong thue phong ro rang, lich su, de chu tro duyet lai.',
                        'Chi tra ve JSON dang {"terms":"..."}.',
                        'Khong tu bia quy dinh phap ly cu the hoac phi phat neu khong co trong du lieu.',
                        'Bao gom tien phong, tien coc, thoi han, dien nuoc, noi quy, bao tri, thanh ly.',
                        '',
                        'Du lieu:',
                        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]),
                ],
            ]);

            $terms = trim((string) ($content['terms'] ?? ''));
            if ($terms === '') {
                throw new \RuntimeException('AI contract terms are empty.');
            }

            return [
                'terms' => $terms,
                'used_ai' => true,
                'fallback_reason' => null,
            ];
        } catch (Throwable $exception) {
            Log::warning('AI contract terms generation failed', ['error' => $exception->getMessage()]);

            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_failed',
            ]);
        }
    }

    public function generateRoomDescription(array $data): array
    {
        $fallback = [
            'description' => $this->fallbackRoomDescription($data),
        ];

        if (!$this->isEnabled()) {
            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_not_configured',
            ]);
        }

        try {
            $content = $this->chatJson([
                [
                    'role' => 'system',
                    'content' => 'Ban la tro ly viet mo ta dang tin phong tro bang tieng Viet. Chi tra ve JSON hop le.',
                ],
                [
                    'role' => 'user',
                    'content' => implode("\n", [
                        'Viet mo ta phong tro ngan gon, thuc te, hap dan nhung khong phong dai.',
                        'Chi tra ve JSON dang {"description":"..."}.',
                        'Khong bia tien ich khong co trong du lieu.',
                        '',
                        'Du lieu:',
                        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]),
                ],
            ]);

            $description = trim((string) ($content['description'] ?? ''));
            if ($description === '') {
                throw new \RuntimeException('AI room description is empty.');
            }

            return [
                'description' => mb_substr(strip_tags($description), 0, 1000),
                'used_ai' => true,
                'fallback_reason' => null,
            ];
        } catch (Throwable $exception) {
            Log::warning('AI room description generation failed', ['error' => $exception->getMessage()]);

            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_failed',
            ]);
        }
    }

    public function summarizeReviews(array $roomData, array $reviews): array
    {
        $fallback = $this->fallbackReviewSummary($reviews);

        if (!$this->isEnabled()) {
            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_not_configured',
            ]);
        }

        try {
            $content = $this->chatJson([
                [
                    'role' => 'system',
                    'content' => 'Ban la tro ly tom tat review phong tro. Chi tra ve JSON hop le.',
                ],
                [
                    'role' => 'user',
                    'content' => implode("\n", [
                        'Tom tat review bang tieng Viet de khach thue doc nhanh.',
                        'Chi tra ve JSON dang {"summary":"...","pros":["..."],"cons":["..."]}.',
                        'Khong bia nhan xet ngoai review.',
                        '',
                        'Phong:',
                        json_encode($roomData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'Reviews:',
                        json_encode($reviews, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ]),
                ],
            ]);

            return [
                'summary' => trim((string) ($content['summary'] ?? $fallback['summary'])),
                'pros' => $this->stringList($content['pros'] ?? $fallback['pros']),
                'cons' => $this->stringList($content['cons'] ?? $fallback['cons']),
                'used_ai' => true,
                'fallback_reason' => null,
            ];
        } catch (Throwable $exception) {
            Log::warning('AI review summary failed', ['error' => $exception->getMessage()]);

            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_failed',
            ]);
        }
    }

    private function dashboardContext(int $tenantId): array
    {
        $rooms = Room::where('tenant_id', $tenantId)->get();
        $paidRevenue = $this->paidRevenueByMonth($tenantId);
        $currentMonth = now()->format('Y-m');
        $previousRevenue = $paidRevenue->slice(-2)->values();
        $currentRevenue = (int) ($previousRevenue->last()['total_revenue'] ?? 0);
        $previousMonthRevenue = (int) ($previousRevenue->first()['total_revenue'] ?? 0);

        return [
            'generated_at' => now()->toDateTimeString(),
            'current_month' => $currentMonth,
            'room_stats' => [
                'total' => $rooms->count(),
                'occupied' => $rooms->where('status', 'occupied')->count(),
                'empty' => $rooms->where('status', 'empty')->count(),
                'overdue' => $rooms->where('status', 'overdue')->count(),
                'maintenance' => $rooms->where('status', 'maintenance')->count(),
            ],
            'revenue_by_month' => $paidRevenue->values()->all(),
            'revenue_change' => [
                'current_revenue' => $currentRevenue,
                'previous_revenue' => $previousMonthRevenue,
                'difference' => $currentRevenue - $previousMonthRevenue,
            ],
            'unpaid' => [
                'count' => UtilityRecord::whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
                    ->whereIn('status', ['sent', 'overdue'])
                    ->count(),
                'total_amount' => $this->unpaidRecords($tenantId)->sum('total_amount'),
            ],
            'long_empty_rooms' => Room::where('tenant_id', $tenantId)
                ->where('status', 'empty')
                ->where('updated_at', '<=', now()->subDays(30))
                ->orderBy('updated_at')
                ->take(8)
                ->get(['room_number', 'price', 'updated_at'])
                ->map(fn (Room $room) => [
                    'room_number' => $room->room_number,
                    'price' => (int) $room->price,
                    'empty_days' => $room->updated_at?->diffInDays(now()),
                ])
                ->values()
                ->all(),
            'expiring_contracts_30_days' => Contract::with(['room:id,room_number', 'resident:id,name'])
                ->where('tenant_id', $tenantId)
                ->where('status', 'active')
                ->whereDate('end_date', '>=', today())
                ->whereDate('end_date', '<=', today()->addDays(30))
                ->orderBy('end_date')
                ->take(8)
                ->get()
                ->map(fn (Contract $contract) => [
                    'room_number' => $contract->room->room_number ?? null,
                    'resident_name' => $contract->resident->name ?? null,
                    'end_date' => $contract->end_date,
                ])
                ->values()
                ->all(),
            'open_tickets' => Ticket::with('room:id,room_number')
                ->where('tenant_id', $tenantId)
                ->whereIn('status', ['pending', 'processing'])
                ->latest()
                ->take(8)
                ->get(['id', 'room_id', 'title', 'category', 'status', 'created_at'])
                ->map(fn (Ticket $ticket) => [
                    'room_number' => $ticket->room->room_number ?? null,
                    'title' => $ticket->title,
                    'category' => $ticket->category,
                    'status' => $ticket->status,
                    'created_at' => $ticket->created_at?->toDateString(),
                ])
                ->values()
                ->all(),
        ];
    }

    private function assistantContext(int $tenantId): array
    {
        return [
            'today' => today()->toDateString(),
            'current_month' => now()->format('Y-m'),
            'rooms' => Room::with(['building:id,name', 'residents' => fn ($query) => $query->where('status', 'active')])
                ->where('tenant_id', $tenantId)
                ->orderBy('room_number')
                ->get()
                ->map(fn (Room $room) => [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'building' => $room->building->name ?? null,
                    'floor' => $room->floor,
                    'status' => $room->status,
                    'price' => (int) $room->price,
                    'area' => (int) $room->area,
                    'residents' => $room->residents->map(fn (Resident $resident) => [
                        'name' => $resident->name,
                        'phone' => $resident->phone,
                        'start_date' => $resident->start_date,
                    ])->values()->all(),
                ])
                ->values()
                ->all(),
            'unpaid_records' => $this->unpaidRecords($tenantId)->take(30)->values()->all(),
            'contracts' => Contract::with(['room:id,room_number', 'resident:id,name,phone'])
                ->where('tenant_id', $tenantId)
                ->orderBy('end_date')
                ->take(50)
                ->get()
                ->map(fn (Contract $contract) => [
                    'contract_code' => $contract->contract_code,
                    'room_number' => $contract->room->room_number ?? null,
                    'resident_name' => $contract->resident->name ?? null,
                    'resident_phone' => $contract->resident->phone ?? null,
                    'start_date' => $contract->start_date,
                    'end_date' => $contract->end_date,
                    'status' => $contract->status,
                    'deposit' => (int) $contract->deposit,
                ])
                ->values()
                ->all(),
            'tickets' => Ticket::with(['room:id,room_number', 'resident:id,name'])
                ->where('tenant_id', $tenantId)
                ->latest()
                ->take(30)
                ->get(['id', 'room_id', 'resident_id', 'title', 'description', 'category', 'status', 'created_at'])
                ->map(fn (Ticket $ticket) => [
                    'room_number' => $ticket->room->room_number ?? null,
                    'resident_name' => $ticket->resident->name ?? null,
                    'title' => $ticket->title,
                    'description' => $ticket->description,
                    'category' => $ticket->category,
                    'status' => $ticket->status,
                    'created_at' => $ticket->created_at?->toDateString(),
                ])
                ->values()
                ->all(),
        ];
    }

    private function paidRevenueByMonth(int $tenantId): Collection
    {
        return UtilityRecord::query()
            ->selectRaw('billing_month, SUM(rooms.price + (new_electricity - old_electricity) * electricity_price + (new_water - old_water) * water_price + ?) as total_revenue', [self::SERVICE_FEE])
            ->join('rooms', 'rooms.id', '=', 'utility_records.room_id')
            ->where('rooms.tenant_id', $tenantId)
            ->where('utility_records.status', 'paid')
            ->groupBy('billing_month')
            ->orderBy('billing_month')
            ->take(12)
            ->get()
            ->map(fn ($row) => [
                'billing_month' => $row->billing_month,
                'total_revenue' => (int) $row->total_revenue,
            ]);
    }

    private function unpaidRecords(int $tenantId): Collection
    {
        return UtilityRecord::with(['room.residents' => fn ($query) => $query->where('status', 'active')])
            ->whereHas('room', fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereIn('status', ['sent', 'overdue'])
            ->orderBy('billing_month')
            ->get()
            ->map(function (UtilityRecord $record) {
                $electricityUsage = max(0, (int) $record->new_electricity - (int) $record->old_electricity);
                $waterUsage = max(0, (int) $record->new_water - (int) $record->old_water);
                $total = (int) ($record->room->price ?? 0)
                    + ($electricityUsage * (int) $record->electricity_price)
                    + ($waterUsage * (int) $record->water_price)
                    + self::SERVICE_FEE;

                return [
                    'room_number' => $record->room->room_number ?? null,
                    'resident_name' => $record->room?->residents->first()?->name,
                    'billing_month' => $record->billing_month,
                    'status' => $record->status,
                    'total_amount' => $total,
                ];
            });
    }

    private function fallbackDashboardInsight(array $context): array
    {
        $stats = $context['room_stats'];
        $unpaid = $context['unpaid'];
        $change = $context['revenue_change'];

        return [
            'summary' => 'AI chua duoc cau hinh nen he thong dang hien nhan xet tu dong theo quy tac. Tong so phong: '
                . $stats['total'] . ', dang thue: ' . $stats['occupied'] . ', phong trong: ' . $stats['empty'] . '.',
            'highlights' => [
                'Doanh thu da thu hien tai: ' . number_format($change['current_revenue']) . ' VND.',
                'Tong cong no chua thu: ' . number_format($unpaid['total_amount']) . ' VND tren ' . $unpaid['count'] . ' hoa don.',
            ],
            'risks' => [
                $stats['empty'] > 0 ? 'Con phong trong can uu tien lap day.' : 'Ty le lap day dang on dinh.',
                $unpaid['count'] > 0 ? 'Con hoa don chua thanh toan can nhac no.' : 'Chua ghi nhan cong no chua thanh toan.',
            ],
            'suggestions' => [
                'Theo doi cac phong trong lau hon 30 ngay de dieu chinh gia hoac mo ta dang tin.',
                'Gui nhac no tu dong cho cac hoa don qua han truoc khi chuyen sang xu ly thu cong.',
            ],
        ];
    }

    private function fallbackTicketAnalysis(string $description, ?string $title): array
    {
        $text = mb_strtolower($title . ' ' . $description);
        $category = match (true) {
            str_contains($text, 'điện') || str_contains($text, 'dien') || str_contains($text, 'ổ cắm') || str_contains($text, 'den') => 'electric',
            str_contains($text, 'nước') || str_contains($text, 'nuoc') || str_contains($text, 'rò') || str_contains($text, 'ống') => 'water',
            str_contains($text, 'giường') || str_contains($text, 'tu') || str_contains($text, 'tủ') || str_contains($text, 'bàn') => 'furniture',
            default => 'maintenance',
        };

        $priority = str_contains($text, 'cháy') || str_contains($text, 'ngập') || str_contains($text, 'rò điện') || str_contains($text, 'không khóa')
            ? 'high'
            : 'medium';

        return [
            'title' => $title ?: mb_substr('Su co bao tri: ' . $description, 0, 150),
            'category' => $category,
            'priority' => $priority,
            'suggestion' => 'Ban quan ly nen kiem tra hien trang, xac nhan muc do anh huong va phan cong tho phu hop.',
            'normalized_description' => $description,
        ];
    }

    private function fallbackContractTerms(array $data): string
    {
        return implode("\n", [
            'DIEU KHOAN HOP DONG THUE PHONG',
            '',
            '1. Ben cho thue dong y cho ben thue su dung phong ' . ($data['room_number'] ?? 'N/A') . ' trong thoi han hop dong da thoa thuan.',
            '2. Tien thue phong la ' . number_format((int) ($data['room_price'] ?? 0)) . ' VND/thang. Tien coc la ' . number_format((int) ($data['deposit'] ?? 0)) . ' VND.',
            '3. Ben thue thanh toan tien phong dung han hang thang; chi phi dien, nuoc va dich vu duoc tinh theo quy dinh cua nha tro.',
            '4. Ben thue co trach nhiem giu gin tai san, bao quan thiet bi va bao ngay cho ban quan ly khi co su co.',
            '5. Hai ben thuc hien viec gia han, thanh ly hoac cham dut hop dong sau khi doi chieu cong no va hien trang phong.',
        ]);
    }

    private function fallbackRoomDescription(array $data): string
    {
        $amenities = $data['amenities'] ?? [];
        $amenityText = is_array($amenities) && count($amenities) > 0
            ? ' Tien ich kem theo: ' . implode(', ', $amenities) . '.'
            : '';

        return 'Phong ' . ($data['room_number'] ?? '') . ' dien tich ' . ((int) ($data['area'] ?? 0))
            . 'm2, gia thue ' . number_format((int) ($data['price'] ?? 0)) . ' VND/thang.'
            . $amenityText . ' Phu hop cho khach thue can khong gian gon gang, de quan ly chi phi hang thang.';
    }

    private function fallbackReviewSummary(array $reviews): array
    {
        if (empty($reviews)) {
            return [
                'summary' => 'Chua co review de tom tat.',
                'pros' => [],
                'cons' => [],
            ];
        }

        $average = collect($reviews)->avg('rating');

        return [
            'summary' => 'Phong co ' . count($reviews) . ' review, diem trung binh khoang ' . round((float) $average, 1) . '/5.',
            'pros' => ['Co du lieu danh gia tu khach thue de tham khao.'],
            'cons' => ['Can doc chi tiet tung review de nam ro diem can cai thien.'],
        ];
    }

    private function fallbackAnswer(array $context, string $question): string
    {
        $lowerQuestion = mb_strtolower($question);

        if (str_contains($lowerQuestion, 'chua dong') || str_contains($lowerQuestion, 'chưa đóng') || str_contains($lowerQuestion, 'no') || str_contains($lowerQuestion, 'nợ')) {
            if (empty($context['unpaid_records'])) {
                return 'Chua co hoa don nao chua thanh toan trong du lieu hien tai.';
            }

            $rooms = collect($context['unpaid_records'])
                ->map(fn ($item) => 'Phong ' . ($item['room_number'] ?? 'N/A') . ' - ' . ($item['resident_name'] ?? 'chua ro cu dan') . ' - ' . number_format($item['total_amount']) . ' VND')
                ->take(10)
                ->implode('; ');

            return 'Cac phong chua thanh toan gom: ' . $rooms . '.';
        }

        if (str_contains($lowerQuestion, 'het hop dong') || str_contains($lowerQuestion, 'hết hợp đồng')) {
            $contracts = collect($context['contracts'])
                ->filter(fn ($contract) => ($contract['status'] ?? null) === 'active' && Carbon::parse($contract['end_date'])->between(today(), today()->addDays(30)))
                ->map(fn ($contract) => 'Phong ' . ($contract['room_number'] ?? 'N/A') . ' - ' . ($contract['resident_name'] ?? 'N/A') . ' - het han ' . $contract['end_date'])
                ->values();

            return $contracts->isEmpty()
                ? 'Chua co hop dong active nao het han trong 30 ngay toi.'
                : 'Hop dong sap het han: ' . $contracts->implode('; ') . '.';
        }

        return 'AI chua duoc cau hinh. Ban co the hoi ve phong chua dong tien hoac hop dong sap het han de he thong tra loi theo quy tac co san.';
    }

    private function chatJson(array $messages): array
    {
        $response = Http::withToken((string) config('services.ai.api_key'))
            ->timeout((int) config('services.ai.timeout', 15))
            ->post(rtrim((string) config('services.ai.base_url'), '/') . '/chat/completions', [
                'model' => config('services.ai.model'),
                'temperature' => 0.2,
                'response_format' => ['type' => 'json_object'],
                'messages' => $messages,
            ]);

        $response->throw();

        $content = $response->json('choices.0.message.content');
        $decoded = is_string($content) ? json_decode($content, true) : null;
        if (!is_array($decoded)) {
            throw new \RuntimeException('AI response is not valid JSON.');
        }

        return $decoded;
    }

    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->take(5)
            ->values()
            ->all();
    }

    private function isEnabled(): bool
    {
        return filled(config('services.ai.api_key'))
            && filled(config('services.ai.base_url'))
            && filled(config('services.ai.model'));
    }
}
