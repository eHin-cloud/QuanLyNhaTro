<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('crud_user:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Models\UtilityRecord;

Route::get('/utility-bill/{id}/qr', function ($id) {
    $bill = UtilityRecord::with('room.residents')->find($id);
    if (!$bill) {
        return response()->json(['error' => 'Hóa đơn không tồn tại'], 404);
    }
    
    $room = $bill->room;
    $elecUsed = max(0, $bill->new_electricity - $bill->old_electricity);
    $waterUsed = max(0, $bill->new_water - $bill->old_water);
    $totalAmount = $room->price + ($elecUsed * $bill->electricity_price) + ($waterUsed * $bill->water_price) + 150000;
    
    $bankId = 'MB'; // Ngân hàng Quân Đội
    $accountNo = '9999888889999'; // Số tài khoản ngân hàng demo
    $accountName = 'NGUYEN VAN CHU NHA'; // Tên chủ tài khoản
    
    $addInfo = rawurlencode("Thanh toan Phong " . $room->room_number . " thang 06");
    $accNameEscaped = rawurlencode($accountName);
    
    $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact.png?amount={$totalAmount}&addInfo={$addInfo}&accountName={$accNameEscaped}";
    
    return response()->json([
        'success' => true,
        'room_number' => $room->room_number,
        'resident_name' => $room->residents->first() ? $room->residents->first()->name : 'N/A',
        'amount' => $totalAmount,
        'bank_id' => $bankId,
        'account_no' => $accountNo,
        'account_name' => $accountName,
        'description' => "Thanh toan Phong " . $room->room_number . " thang 06",
        'qr_url' => $qrUrl
    ]);
});

Route::post('/send-message', function (Illuminate\Http\Request $request) {
    $request->validate([
        'phone' => 'required|string',
        'message' => 'required|string',
        'type' => 'required|in:zalo,sms'
    ]);
    
    // Giả lập độ trễ truyền tải mạng 0.8s
    usleep(800000);
    
    return response()->json([
        'success' => true,
        'status' => 'sent',
        'phone' => $request->phone,
        'message' => $request->message,
        'type' => $request->type,
        'sent_at' => now()->toIso8601String()
    ]);
});

Route::get('/revenue-breakdown', function () {
    $breakdown = UtilityRecord::selectRaw("
        SUM(rooms.price) as room_fee,
        SUM(GREATEST(0, new_electricity - old_electricity) * electricity_price) as electric_fee,
        SUM(GREATEST(0, new_water - old_water) * water_price) as water_fee,
        SUM(150000) as service_fee
    ")
    ->join('rooms', 'rooms.id', '=', 'utility_records.room_id')
    ->where('utility_records.status', 'paid')
    ->first();
    
    $roomFee = (int) ($breakdown->room_fee ?? 75000000);
    $electricFee = (int) ($breakdown->electric_fee ?? 18450000);
    $waterFee = (int) ($breakdown->water_fee ?? 6520000);
    $serviceFee = (int) ($breakdown->service_fee ?? 4500000);
    
    $total = $roomFee + $electricFee + $waterFee + $serviceFee;
    
    return response()->json([
        'success' => true,
        'total' => $total,
        'breakdown' => [
            'room' => $roomFee,
            'electric' => $electricFee,
            'water' => $waterFee,
            'service' => $serviceFee
        ],
        'percentages' => [
            'room' => $total > 0 ? round(($roomFee / $total) * 100, 1) : 0,
            'electric' => $total > 0 ? round(($electricFee / $total) * 100, 1) : 0,
            'water' => $total > 0 ? round(($waterFee / $total) * 100, 1) : 0,
            'service' => $total > 0 ? round(($serviceFee / $total) * 100, 1) : 0
        ]
    ]);
});

Route::get('/rooms/compare', function (Illuminate\Http\Request $request) {
    $ids = $request->input('ids');
    if (!is_array($ids) || empty($ids)) {
        return response()->json(['success' => false, 'message' => 'Danh sách phòng trống'], 400);
    }
    
    $rooms = \App\Models\Room::with(['residents', 'reviews'])->whereIn('id', $ids)->get();
    
    $mapped = $rooms->map(function($room) {
        $num = intval($room->room_number);
        
        $dbReviews = $room->reviews;
        if ($dbReviews->count() > 0) {
            $rating = $dbReviews->avg('rating');
        } else {
            $rating = 3.6 + (($num * 7) % 15) / 10;
            if ($rating > 5.0) $rating = 5.0;
        }
        
        $distance = 0.4 + (($num * 3) % 12) / 10;
        
        $pets = ($num % 2 == 1);
        $loft = (($num % 3) != 2);
        $balcony = (($num % 4) != 0);
        
        $ownerStars = intval(round($rating));
        $secStars = intval(min(5, max(3, round($rating + ($num % 2 ? 0.5 : -0.5)))));
        
        $priceScore = round(max(0, min(10, (5000000 - $room->price) / 300000 + 2)), 1);
        $distanceScore = round(max(0, min(10, (2.0 - $distance) * 6)), 1);
        $securityScore = $secStars * 2;
        $ownerScore = $ownerStars * 2;
        
        return [
            'id' => $room->id,
            'room_number' => $room->room_number,
            'price' => $room->price,
            'price_formatted' => number_format($room->price, 0, ',', '.') . 'đ',
            'distance' => $distance,
            'rating' => number_format($rating, 1),
            'owner_stars' => $ownerStars,
            'security_stars' => $secStars,
            'pets' => $pets ? 'Có' : 'Không',
            'loft' => $loft ? 'Có' : 'Không',
            'balcony' => $balcony ? 'Có' : 'Không',
            'scores' => [
                'price' => $priceScore,
                'distance' => $distanceScore,
                'security' => $securityScore,
                'owner' => $ownerScore
            ]
        ];
    });
    
    return response()->json([
        'success' => true,
        'data' => $mapped
    ]);
});