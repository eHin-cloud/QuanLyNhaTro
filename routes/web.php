<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrudUserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminActivityLogController;
use App\Http\Controllers\ResidentPortalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('dashboard', [CrudUserController::class, 'dashboard']);

Route::get('login', [CrudUserController::class, 'login'])->name('login');
Route::post('login', [CrudUserController::class, 'authUser'])->name('user.authUser');

Route::get('create', [CrudUserController::class, 'createUser'])->name('user.createUser');
Route::post('create', [CrudUserController::class, 'postUser'])->name('user.postUser');

Route::middleware('role:admin')->group(function () {
    Route::get('read', [CrudUserController::class, 'readUser'])->name('user.readUser');
    Route::get('delete', [CrudUserController::class, 'deleteUser'])->name('user.deleteUser');
    Route::get('update', [CrudUserController::class, 'updateUser'])->name('user.updateUser');
    Route::post('update', [CrudUserController::class, 'postUpdateUser'])->name('user.postUpdateUser');
    Route::post('users/role', [CrudUserController::class, 'updateRole'])->name('user.updateRole');
    Route::get('list', [CrudUserController::class, 'listUser'])->name('user.list');
});

Route::get('signout', [CrudUserController::class, 'signOut'])->name('signout');

Route::get('/', function () {
    return view('index');
})->name('smartroom.portal');

Route::get('/smartroom/resident', [ResidentPortalController::class, 'index'])->name('smartroom.resident');
Route::post('/smartroom/resident/tickets/analyze', [ResidentPortalController::class, 'analyzeTicket'])->name('smartroom.resident.tickets.analyze');
Route::post('/smartroom/resident/tickets', [ResidentPortalController::class, 'storeTicket'])->name('smartroom.resident.tickets.store');
Route::get('/smartroom/resident/bills/{id}/qr', [ResidentPortalController::class, 'billQr'])->name('smartroom.resident.bills.qr');


Route::middleware('admin')->group(function () {
    Route::get('/smartroom/admin', [AdminDashboardController::class, 'index'])->name('smartroom.admin');
    Route::get('/smartroom/admin/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::post('/smartroom/admin/payments/{payment}', [PaymentController::class, 'update'])->name('admin.payments.update');
    Route::post('/smartroom/admin/utility', [AdminDashboardController::class, 'storeUtility'])->name('smartroom.admin.utility.store');
    Route::post('/smartroom/admin/utility/bulk', [AdminDashboardController::class, 'storeUtilityBulk'])->name('smartroom.admin.utility.bulk_store');
    Route::post('/smartroom/admin/resident', [AdminDashboardController::class, 'storeResident'])->name('smartroom.admin.resident.store');
    Route::put('/smartroom/admin/resident/{id}', [AdminDashboardController::class, 'updateResident'])->name('smartroom.admin.resident.update');
    Route::post('/smartroom/admin/utility/{id}/pay', [AdminDashboardController::class, 'payUtility'])->name('smartroom.admin.utility.pay');
    Route::get('/smartroom/admin/utility/{id}/print', [AdminDashboardController::class, 'printUtility'])->name('smartroom.admin.utility.print');
    Route::post('/smartroom/admin/utility/{id}/notify', [AdminDashboardController::class, 'notifyUtility'])->name('smartroom.admin.utility.notify');

    Route::middleware('role:landlord')->group(function () {
        Route::post('/smartroom/admin/ai/dashboard-insight', [AdminDashboardController::class, 'aiDashboardInsight'])->name('smartroom.admin.ai.dashboard_insight');
        Route::post('/smartroom/admin/ai/assistant', [AdminDashboardController::class, 'aiAssistant'])->name('smartroom.admin.ai.assistant');
        Route::post('/smartroom/admin/ai/contract-terms', [AdminDashboardController::class, 'aiContractTerms'])->name('smartroom.admin.ai.contract_terms');
        Route::get('/smartroom/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/smartroom/admin/activity-logs', [AdminActivityLogController::class, 'index'])->name('admin.activity_logs.index');
        Route::get('/smartroom/admin/payments/export', [PaymentController::class, 'export'])->name('admin.payments.export');
        Route::delete('/smartroom/admin/resident/{id}', [AdminDashboardController::class, 'deleteResident'])->name('smartroom.admin.resident.delete');
        Route::post('/smartroom/admin/utility/auto-remind', [AdminDashboardController::class, 'autoRemindUtilities'])->name('smartroom.admin.utility.auto_remind');
        Route::post('/smartroom/admin/notifications/contracts', [AdminDashboardController::class, 'notifyContracts'])->name('smartroom.admin.notifications.contracts');
        Route::post('/smartroom/admin/notifications/maintenance', [AdminDashboardController::class, 'notifyMaintenance'])->name('smartroom.admin.notifications.maintenance');
        Route::post('/smartroom/admin/notifications/run-all', [AdminDashboardController::class, 'notifyAll'])->name('smartroom.admin.notifications.run_all');
    });

    // Room Management
    Route::prefix('smartroom/admin/rooms')->name('admin.rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/store', [RoomController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [RoomController::class, 'update'])->name('update');
        Route::post('/description/ai', [RoomController::class, 'generateDescription'])->middleware('role:landlord')->name('description.ai');
        Route::delete('/{id}/delete', [RoomController::class, 'destroy'])->middleware('role:landlord')->name('destroy');
    });

    // Equipment Management
    Route::prefix('smartroom/admin/equipment')->name('admin.equipment.')->group(function () {
        Route::get('/', [EquipmentController::class, 'index'])->name('index');
        Route::get('/store', fn () => redirect()->route('admin.equipment.index'))->name('store.redirect');
        Route::post('/store', [EquipmentController::class, 'store'])->name('store');
        Route::post('/{id}/update', [EquipmentController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [EquipmentController::class, 'destroy'])->middleware('role:landlord')->name('destroy');
        Route::post('/allocate', [EquipmentController::class, 'allocate'])->name('allocate');
        Route::post('/recover', [EquipmentController::class, 'recover'])->name('recover');
    });

    // Online Contracts
    Route::post('/smartroom/admin/contract', [AdminDashboardController::class, 'storeContract'])->name('smartroom.admin.contract.store');
    Route::delete('/smartroom/admin/contract/{id}', [AdminDashboardController::class, 'deleteContract'])->middleware('role:landlord')->name('smartroom.admin.contract.delete');

    // Contact Requests Management
    Route::post('/smartroom/admin/contact-request/{id}/status', [AdminDashboardController::class, 'updateContactRequestStatus'])->name('smartroom.admin.contact_request.status');
    Route::delete('/smartroom/admin/contact-request/{id}', [AdminDashboardController::class, 'deleteContactRequest'])->middleware('role:landlord')->name('smartroom.admin.contact_request.delete');

    // Resident Relative Management (AJAX JSON APIs)
    Route::get('/smartroom/admin/resident/{residentId}/relatives', [AdminDashboardController::class, 'getRelatives'])->name('smartroom.admin.resident.relatives');
    Route::post('/smartroom/admin/resident/{residentId}/relative', [AdminDashboardController::class, 'storeRelative'])->name('smartroom.admin.resident.relative.store');
    Route::put('/smartroom/admin/relative/{id}', [AdminDashboardController::class, 'updateRelative'])->name('smartroom.admin.relative.update');
    Route::delete('/smartroom/admin/relative/{id}', [AdminDashboardController::class, 'deleteRelative'])->middleware('role:landlord')->name('smartroom.admin.relative.delete');
});
Route::get('/smartroom/contract/{id}/sign', [AdminDashboardController::class, 'signContractView'])->name('smartroom.contract.sign_view');
Route::post('/smartroom/contract/{id}/sign', [AdminDashboardController::class, 'signContract'])->name('smartroom.contract.sign');
Route::post('/renty/contact-request', [AdminDashboardController::class, 'storeContactRequest'])->name('renty.contact_request.store');

$rentyPage = function () {
    $rooms = \App\Models\Room::with(['building', 'tenant', 'residents', 'reviews'])->get();
    
    $mappedRooms = $rooms->map(function($room) {
        $num = intval($room->room_number);
        
        $dbReviews = $room->reviews;
        if ($dbReviews->count() > 0) {
            $rating = $dbReviews->avg('rating');
        } else {
            $rating = 3.6 + (($num * 7) % 15) / 10;
            if ($rating > 5.0) $rating = 5.0;
        }
        
        $distance = 0.4 + (($num * 3) % 12) / 10;
        $building = $room->building;
        $buildingName = $building?->name ?? 'Rentry Review';
        $buildingAddress = $building?->address ?? 'Khu nhà trọ đang cập nhật địa chỉ';
        $areaName = str_contains($buildingAddress, 'Thanh Xuân')
            ? 'Thanh Xuân'
            : (str_contains($buildingAddress, 'Cầu Giấy')
                ? 'Cầu Giấy'
                : (str_contains($buildingAddress, 'Quận 10') ? 'Quận 10' : 'khu vực trung tâm'));
        $amenities = collect($room->amenities ?? [])->map(fn ($item) => mb_strtolower($item));

        $pets = $amenities->contains(fn ($item) => str_contains($item, 'thú cưng')) || ($num % 2 == 1);
        $loft = $amenities->contains(fn ($item) => str_contains($item, 'gác') || str_contains($item, 'gac')) || (($num % 3) != 2);
        $balcony = $amenities->contains(fn ($item) => str_contains($item, 'ban công') || str_contains($item, 'ban cong')) || (($num % 4) != 0);
        
        $ownerStars = intval(round($rating));
        $ownerRating = str_repeat('⭐', $ownerStars) . str_repeat('☆', 5 - $ownerStars) . " ($ownerStars/5)";
        
        $secStars = intval(min(5, max(3, round($rating + ($num % 2 ? 0.5 : -0.5)))));
        $secRating = str_repeat('⭐', $secStars) . str_repeat('☆', 5 - $secStars) . " ($secStars/5)";
        
        $title = $buildingName . " - Phòng " . $room->room_number;
        $address = $buildingAddress . " (Cách điểm tiện ích gần nhất " . number_format($distance, 1) . "km)";
        $area = (int) ($room->area ?? (22 + ($num % 9)));
        $locationDescription = ($building?->description ?: "Nằm tại khu vực {$areaName}, thuận tiện di chuyển và sinh hoạt hằng ngày.") . " Địa chỉ: {$buildingAddress}.";
        $sceneryDescription = $balcony
            ? "Khu {$areaName} có không gian quanh phòng thoáng hơn nhờ ban công, có ánh sáng tự nhiên, phù hợp người thích phòng sáng và có chỗ phơi đồ."
            : "Khu {$areaName} yên tĩnh, phù hợp học tập và nghỉ ngơi; lối đi trong nhà gọn, có camera và khóa an ninh.";
        $spaceDescription = "Phòng rộng khoảng {$area}m², bố trí dạng " . ($loft ? 'có gác lửng để tách khu ngủ và sinh hoạt' : 'một mặt bằng dễ sắp xếp đồ') . ", phù hợp 1-2 người ở với không gian sinh hoạt gọn gàng.";
        
        $reviewsList = $dbReviews->map(function($rev) {
            return [
                'author_name' => $rev->author_name,
                'rating' => $rev->rating,
                'comment' => $rev->comment,
                'created_at' => $rev->created_at->format('d/m/Y H:i')
            ];
        })->toArray();

        $uploadedImages = collect($room->images ?? []);
        if ($room->image) {
            $uploadedImages->prepend($room->image);
        }

        $imageUrls = $uploadedImages
            ->filter()
            ->unique()
            ->values()
            ->map(fn ($path) => \Illuminate\Support\Facades\Storage::url($path))
            ->all();

        if (empty($imageUrls)) {
            $fallbackSets = [
                [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80',
                ],
                [
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1200&q=80',
                ],
                [
                    'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560448075-bb485b067938?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1200&q=80',
                ],
            ];

            $imageUrls = $fallbackSets[$num % count($fallbackSets)];
        }
        
        return [
            'id' => $room->id,
            'room_number' => $room->room_number,
            'price' => $room->price,
            'status' => $room->status,
            'rating' => number_format($rating, 1),
            'distance' => $distance,
            'pets' => $pets ? 'true' : 'false',
            'loft' => $loft ? 'true' : 'false',
            'balcony' => $balcony ? 'true' : 'false',
            'owner' => $ownerRating,
            'sec' => $secRating,
            'title' => $title,
            'address' => $address,
            'area_name' => $areaName,
            'area' => $area,
            'location_description' => $locationDescription,
            'scenery_description' => $sceneryDescription,
            'space_description' => $spaceDescription,
            'area_text' => $area . ' m²',
            'pets_txt' => $pets ? 'Có' : 'Không',
            'loft_txt' => $loft ? 'Có' : 'Không',
            'balcony_txt' => $balcony ? 'Có' : 'Không',
            'cover_image' => $imageUrls[0],
            'image_urls' => $imageUrls,
            'reviews' => $reviewsList
        ];
    });

    $mappedRooms = $mappedRooms->map(function ($room) use ($mappedRooms) {
        $peerRooms = $mappedRooms->filter(function ($peer) use ($room) {
            return $peer['id'] !== $room['id']
                && $peer['area_name'] === $room['area_name']
                && abs((int) $peer['area'] - (int) $room['area']) <= 5;
        });

        $averagePrice = (int) round($peerRooms->count() > 0 ? $peerRooms->avg('price') : $mappedRooms->avg('price'));
        $priceDiffPercent = $averagePrice > 0 ? (($room['price'] - $averagePrice) / $averagePrice) * 100 : 0;

        $room['area_average_price'] = $averagePrice;
        $room['price_diff_percent'] = round($priceDiffPercent, 1);
        $room['price_warning'] = null;

        if ($priceDiffPercent <= -25) {
            $room['price_warning'] = [
                'type' => 'low',
                'label' => 'Giá thấp bất thường',
                'message' => 'Thấp hơn khoảng ' . abs(round($priceDiffPercent)) . '% so với nhóm phòng cùng khu vực/diện tích. Nên kiểm tra kỹ ảnh, phí phát sinh và điều kiện cọc.',
            ];
        } elseif ($priceDiffPercent >= 25) {
            $room['price_warning'] = [
                'type' => 'high',
                'label' => 'Giá cao hơn mặt bằng',
                'message' => 'Cao hơn khoảng ' . round($priceDiffPercent) . '% so với nhóm phòng cùng khu vực/diện tích. Nên so sánh thêm tiện ích và vị trí trước khi liên hệ.',
            ];
        }

        return $room;
    });

    $recentReviews = \App\Models\Review::with('room')->latest()->take(5)->get();

    return view('rentry.rentry', [
        'rooms' => $mappedRooms,
        'recentReviews' => $recentReviews
    ]);
};

Route::get('/renty', $rentyPage)->name('renty.user');

Route::post('/renty/room/{id}/review', function (Illuminate\Http\Request $request, $id) {
    $request->validate([
        'author_name' => 'required|string|max:255',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string'
    ]);

    \App\Models\Review::create([
        'room_id' => $id,
        'author_name' => $request->author_name,
        'rating' => $request->rating,
        'comment' => $request->comment
    ]);

    return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá thực tế!');
})->name('renty.room.review.store');
