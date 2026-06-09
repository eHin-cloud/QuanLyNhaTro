<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('rent:send-reminders', function () {
    $this->info('Starting automatic rent reminders scan for unpaid bills...');

    $currentMonth = now()->format('Y-m');
    $tenantIds = \App\Models\Tenant::query()->pluck('id');

    if ($tenantIds->isEmpty()) {
        $this->info('No tenants found.');
        return;
    }

    $sentCount = 0;
    foreach ($tenantIds as $tenantId) {
        $logs = app(\App\Services\NotificationService::class)
            ->sendPaymentReminders((int) $tenantId, $currentMonth, ['zalo']);

        $sentCount += $logs->count();
    }

    if ($sentCount === 0) {
        $this->info("No unpaid bills found for the month {$currentMonth}.");
        return;
    }

    $this->info("Finished sending {$sentCount} reminders.");
})->purpose('Scan and send Zalo rent payment reminders automatically on the 10th of each month for unpaid bills');
