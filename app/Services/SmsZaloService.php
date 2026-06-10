<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsZaloService
{
    /**
     * Gửi tin nhắn SMS qua API Gateway (ghi log vào sms_zalo channel).
     */
    public function sendSms(string $phone, string $message): bool
    {
        Log::channel('sms_zalo')->info("SMS DIspatched to {$phone}: {$message}");
        return true;
    }

    /**
     * Gửi tin nhắn Zalo ZNS qua API Gateway (ghi log vào sms_zalo channel).
     */
    public function sendZalo(string $phone, string $message): bool
    {
        Log::channel('sms_zalo')->info("Zalo ZNS Dispatched to {$phone}: {$message}");
        return true;
    }
}
