<?php

namespace App\Jobs;

use App\Services\SmsZaloService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsZaloNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $channel,
        protected string $recipient,
        protected string $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SmsZaloService $smsZaloService): void
    {
        Log::info("Executing background job SendSmsZaloNotification", [
            'channel' => $this->channel,
            'recipient' => $this->recipient
        ]);

        if ($this->channel === 'sms') {
            $smsZaloService->sendSms($this->recipient, $this->message);
        } elseif ($this->channel === 'zalo') {
            $smsZaloService->sendZalo($this->recipient, $this->message);
        }
    }
}
