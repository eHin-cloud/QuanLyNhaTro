<?php

namespace App\Services;

use App\Models\Resident;
use App\Models\Room;
use App\Models\UtilityRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiReminderService
{
    public function generatePaymentReminder(
        UtilityRecord $record,
        Room $room,
        Resident $resident,
        int $totalAmount,
        string $channel,
        ?Carbon $now = null
    ): array {
        $now ??= Carbon::now();
        $fallback = $this->fallbackPaymentReminder($record, $room, $resident, $totalAmount);

        if (!$this->isEnabled()) {
            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_not_configured',
            ]);
        }

        try {
            $response = Http::withToken((string) config('services.ai.api_key'))
                ->timeout((int) config('services.ai.timeout', 15))
                ->post(rtrim((string) config('services.ai.base_url'), '/') . '/chat/completions', [
                    'model' => config('services.ai.model'),
                    'temperature' => 0.4,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Ban la tro ly quan ly nha tro. Chi tra ve JSON hop le voi subject va message.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $this->buildPrompt($record, $room, $resident, $totalAmount, $channel, $now),
                        ],
                    ],
                ]);

            $response->throw();

            $content = $response->json('choices.0.message.content');
            $decoded = is_string($content) ? json_decode($content, true) : null;

            $subject = trim((string) ($decoded['subject'] ?? ''));
            $message = trim((string) ($decoded['message'] ?? ''));

            if ($subject === '' || $message === '') {
                throw new \RuntimeException('AI response missing subject or message.');
            }

            return [
                'subject' => mb_substr($subject, 0, 180),
                'message' => $message,
                'used_ai' => true,
                'fallback_reason' => null,
            ];
        } catch (Throwable $exception) {
            Log::warning('AI payment reminder generation failed', [
                'utility_record_id' => $record->id,
                'room_id' => $room->id,
                'resident_id' => $resident->id,
                'error' => $exception->getMessage(),
            ]);

            return array_merge($fallback, [
                'used_ai' => false,
                'fallback_reason' => 'ai_failed',
            ]);
        }
    }

    public function fallbackPaymentReminder(
        UtilityRecord $record,
        Room $room,
        Resident $resident,
        int $totalAmount
    ): array {
        $subject = 'Nhac thanh toan hoa don phong ' . $room->room_number;
        $message = 'Phong ' . $room->room_number . ' co hoa don thang ' . $record->billing_month
            . ' chua thanh toan. Tong tien: ' . number_format($totalAmount)
            . ' VND. Vui long thanh toan truoc ngay 10.';

        return [
            'subject' => $subject,
            'message' => $message,
        ];
    }

    private function buildPrompt(
        UtilityRecord $record,
        Room $room,
        Resident $resident,
        int $totalAmount,
        string $channel,
        Carbon $now
    ): string {
        $dueDate = Carbon::parse($record->billing_month . '-10')->endOfDay();
        $overdueDays = $dueDate->lt($now) ? $dueDate->diffInDays($now) : 0;
        $tone = match (true) {
            $overdueDays >= 15 => 'qua_han_lau',
            $overdueDays > 0 => 'moi_qua_han',
            default => 'sap_toi_han',
        };

        return implode("\n", [
            'Hay viet noi dung nhac thanh toan bang tieng Viet, lich su, ngan gon.',
            'Chi tra ve JSON dang {"subject":"...","message":"..."}.',
            '',
            'Thong tin:',
            '- Cu dan: ' . $resident->name,
            '- Phong: ' . $room->room_number,
            '- Thang hoa don: ' . $record->billing_month,
            '- Tong tien: ' . number_format($totalAmount) . ' VND',
            '- Trang thai: ' . $record->status,
            '- So ngay qua han: ' . $overdueDays,
            '- Nhom noi dung: ' . $tone,
            '- Kenh gui: ' . $channel,
            '',
            'Yeu cau:',
            '- Khong de doa.',
            '- Khong tu bia phi phat.',
            '- Co loi cam on.',
            '- Neu la SMS hoac Zalo thi ngan gon.',
            '- Neu la email thi day du hon nhung khong dai dong.',
        ]);
    }

    private function isEnabled(): bool
    {
        return filled(config('services.ai.api_key'))
            && filled(config('services.ai.base_url'))
            && filled(config('services.ai.model'));
    }
}
