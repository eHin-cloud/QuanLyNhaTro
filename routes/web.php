<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrudUserController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\RoomController;

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

Route::get('read', [CrudUserController::class, 'readUser'])->name('user.readUser');

Route::get('delete', [CrudUserController::class, 'deleteUser'])->name('user.deleteUser');

Route::get('update', [CrudUserController::class, 'updateUser'])->name('user.updateUser');
Route::post('update', [CrudUserController::class, 'postUpdateUser'])->name('user.postUpdateUser');

Route::get('list', [CrudUserController::class, 'listUser'])->name('user.list');

Route::get('signout', [CrudUserController::class, 'signOut'])->name('signout');

Route::get('/', function () {
    return view('index');
})->name('smartroom.portal');


Route::middleware('admin')->group(function () {
    Route::get('/smartroom/admin', [AdminDashboardController::class, 'index'])->name('smartroom.admin');
    Route::post('/smartroom/admin/utility', [AdminDashboardController::class, 'storeUtility'])->name('smartroom.admin.utility.store');
    Route::post('/smartroom/admin/utility/bulk', [AdminDashboardController::class, 'storeUtilityBulk'])->name('smartroom.admin.utility.bulk_store');
    Route::post('/smartroom/admin/resident', [AdminDashboardController::class, 'storeResident'])->name('smartroom.admin.resident.store');
    Route::put('/smartroom/admin/resident/{id}', [AdminDashboardController::class, 'updateResident'])->name('smartroom.admin.resident.update');
    Route::delete('/smartroom/admin/resident/{id}', [AdminDashboardController::class, 'deleteResident'])->name('smartroom.admin.resident.delete');
    Route::post('/smartroom/admin/utility/{id}/pay', [AdminDashboardController::class, 'payUtility'])->name('smartroom.admin.utility.pay');
    Route::get('/smartroom/admin/utility/{id}/print', [AdminDashboardController::class, 'printUtility'])->name('smartroom.admin.utility.print');
    Route::post('/smartroom/admin/utility/{id}/notify', [AdminDashboardController::class, 'notifyUtility'])->name('smartroom.admin.utility.notify');

    // Room Management
    Route::prefix('smartroom/admin/rooms')->name('admin.rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/store', [RoomController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [RoomController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [RoomController::class, 'destroy'])->name('destroy');
    });

    // Online Contracts
    Route::post('/smartroom/admin/contract', [AdminDashboardController::class, 'storeContract'])->name('smartroom.admin.contract.store');
    Route::delete('/smartroom/admin/contract/{id}', [AdminDashboardController::class, 'deleteContract'])->name('smartroom.admin.contract.delete');

    // Contact Requests Management
    Route::post('/smartroom/admin/contact-request/{id}/status', [AdminDashboardController::class, 'updateContactRequestStatus'])->name('smartroom.admin.contact_request.status');
    Route::delete('/smartroom/admin/contact-request/{id}', [AdminDashboardController::class, 'deleteContactRequest'])->name('smartroom.admin.contact_request.delete');

    // Resident Relative Management (AJAX JSON APIs)
    Route::get('/smartroom/admin/resident/{residentId}/relatives', [AdminDashboardController::class, 'getRelatives'])->name('smartroom.admin.resident.relatives');
    Route::post('/smartroom/admin/resident/{residentId}/relative', [AdminDashboardController::class, 'storeRelative'])->name('smartroom.admin.resident.relative.store');
    Route::put('/smartroom/admin/relative/{id}', [AdminDashboardController::class, 'updateRelative'])->name('smartroom.admin.relative.update');
    Route::delete('/smartroom/admin/relative/{id}', [AdminDashboardController::class, 'deleteRelative'])->name('smartroom.admin.relative.delete');
});
Route::get('/smartroom/contract/{id}/sign', [AdminDashboardController::class, 'signContractView'])->name('smartroom.contract.sign_view');
Route::post('/smartroom/contract/{id}/sign', [AdminDashboardController::class, 'signContract'])->name('smartroom.contract.sign');
Route::post('/renty/contact-request', [AdminDashboardController::class, 'storeContactRequest'])->name('renty.contact_request.store');

Route::get('/renty/user', function () {
    $rooms = \App\Models\Room::with(['residents', 'reviews'])->get();
    
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
        
        $pets = ($num % 2 == 1);
        $loft = (($num % 3) != 2);
        $balcony = (($num % 4) != 0);
        
        $ownerStars = intval(round($rating));
        $ownerRating = str_repeat('⭐', $ownerStars) . str_repeat('☆', 5 - $ownerStars) . " ($ownerStars/5)";
        
        $secStars = intval(min(5, max(3, round($rating + ($num % 2 ? 0.5 : -0.5)))));
        $secRating = str_repeat('⭐', $secStars) . str_repeat('☆', 5 - $secStars) . " ($secStars/5)";
        
        $title = "SmartRoom Cầu Giấy - Phòng " . $room->room_number;
        $address = "Số 12 Ngõ 105 Xuân Thủy, Cầu Giấy (Cách ĐH Sư Phạm " . $distance . "km)";
        
        $reviewsList = $dbReviews->map(function($rev) {
            return [
                'author_name' => $rev->author_name,
                'rating' => $rev->rating,
                'comment' => $rev->comment,
                'created_at' => $rev->created_at->format('d/m/Y H:i')
            ];
        })->toArray();
        
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
            'pets_txt' => $pets ? 'Có' : 'Không',
            'loft_txt' => $loft ? 'Có' : 'Không',
            'balcony_txt' => $balcony ? 'Có' : 'Không',
            'reviews' => $reviewsList
        ];
    });

    $recentReviews = \App\Models\Review::with('room')->latest()->take(5)->get();

    return view('user.user', [
        'rooms' => $mappedRooms,
        'recentReviews' => $recentReviews
    ]);
})->name('renty.user');

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