<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('rent:send-reminders', function () {
    $this->info("Starting automatic rent reminders scan for unpaid bills...");
    
    $currentMonth = now()->format('Y-m');
    
    $unpaidBills = \App\Models\UtilityRecord::with('room.residents')
        ->where('status', '!=', 'paid')
        ->where('billing_month', $currentMonth)
        ->get();
        
    if ($unpaidBills->isEmpty()) {
        $this->info("No unpaid bills found for the month {$currentMonth}.");
        return;
    }
    
    $sentCount = 0;
    foreach ($unpaidBills as $bill) {
        $room = $bill->room;
        if (!$room) continue;
        
        $resident = $room->residents->first();
        if (!$resident) continue;
        
        $phone = $resident->phone ?? '0987654321';
        $residentName = $resident->name;
        
        // Calculate Total
        $elecUsed = max(0, $bill->new_electricity - $bill->old_electricity);
        $waterUsed = max(0, $bill->new_water - $bill->old_water);
        $totalAmount = $room->price + ($elecUsed * $bill->electricity_price) + ($waterUsed * $bill->water_price) + 150000;
        $totalFormatted = number_format($totalAmount, 0, ',', '.') . 'đ';
        
        // QR payment URL
        $bankId = 'MB';
        $accountNo = '9999888889999';
        $accountName = 'NGUYEN VAN CHU NHA';
        $addInfo = rawurlencode("Thanh toan Phong " . $room->room_number . " thang " . now()->format('m'));
        $accNameEscaped = rawurlencode($accountName);
        $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$totalAmount}&addInfo={$addInfo}&accountName={$accNameEscaped}";
        
        // Build Zalo message template
        $message = "📢 [SMARTROOM REMINDER] Kính gửi Anh/Chị {$residentName} (Phòng {$room->room_number}). Hệ thống nhận thấy hóa đơn tiền trọ tháng " . now()->format('m/Y') . " của phòng mình chưa được hoàn tất. Tổng số tiền cần thanh toán là {$totalFormatted}. Kính mong Anh/Chị thanh toán trước ngày 10 để tránh trễ hạn. Link quét QR VietQR thanh toán nhanh: {$qrUrl}. Trân trọng cảm ơn!";
        
        // Log to laravel log
        \Illuminate\Support\Facades\Log::info("Auto Zalo Sent to Room {$room->room_number} ({$residentName}): {$message}");
        
        // Update bill status to 'sent'
        $bill->update(['status' => 'sent']);
        
        $this->info("Successfully sent Zalo reminder to Room {$room->room_number} ({$residentName}) - Phone: {$phone}");
        $sentCount++;
    }
    
    $this->info("Finished sending {$sentCount} reminders.");
})->purpose('Scan and send Zalo rent payment reminders automatically on the 10th of each month for unpaid bills');
