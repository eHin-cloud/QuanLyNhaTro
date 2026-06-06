<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VisitorController;
use App\Http\Controllers\Api\TenantAdminController;
use App\Http\Controllers\Api\ResidentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// ==========================================
// 1. PUBLIC ROUTES (Khách vãng lai & Tìm trọ)
// ==========================================
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/renty/rooms', [VisitorController::class, 'index']);
Route::get('/renty/rooms/map', [VisitorController::class, 'map']);
Route::get('/renty/rooms/{id}/reviews', [VisitorController::class, 'reviews']);
Route::post('/renty/rooms/compare', [VisitorController::class, 'compare']);

// ==========================================
// 2. AUTHENTICATED ROUTES (Đã đăng nhập)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth profile & logout
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Khách hàng / Cư dân đã đăng nhập viết đánh giá phòng trọ
    Route::post('/renty/rooms/{id}/reviews', [VisitorController::class, 'storeReview']);

    // ------------------------------------------
    // A. PHÂN HỆ CHỦ TRỌ / QUẢN LÝ (Tenant Admin)
    // ------------------------------------------
    Route::middleware('role:landlord')->prefix('admin')->group(function () {
        // Thống kê Dashboard
        Route::get('/dashboard', [TenantAdminController::class, 'dashboard']);

        // CRUD Tòa nhà (Buildings)
        Route::get('/buildings', [TenantAdminController::class, 'listBuildings']);
        Route::post('/buildings', [TenantAdminController::class, 'storeBuilding']);
        Route::put('/buildings/{id}', [TenantAdminController::class, 'updateBuilding']);
        Route::delete('/buildings/{id}', [TenantAdminController::class, 'deleteBuilding']);

        // CRUD Phòng trọ (Rooms)
        Route::get('/rooms', [TenantAdminController::class, 'listRooms']);
        Route::post('/rooms', [TenantAdminController::class, 'storeRoom']);
        Route::put('/rooms/{id}', [TenantAdminController::class, 'updateRoom']);
        Route::delete('/rooms/{id}', [TenantAdminController::class, 'deleteRoom']);

        // Sơ đồ phòng trực quan (Room Matrix)
        Route::get('/rooms/matrix', [TenantAdminController::class, 'roomMatrix']);

        // CRUD Cư dân (Residents)
        Route::get('/residents', [TenantAdminController::class, 'listResidents']);
        Route::post('/residents', [TenantAdminController::class, 'storeResident']);
        Route::put('/residents/{id}', [TenantAdminController::class, 'updateResident']);
        Route::delete('/residents/{id}', [TenantAdminController::class, 'deleteResident']);

        // Hợp đồng online
        Route::get('/contracts', [TenantAdminController::class, 'listContracts']);
        Route::post('/contracts', [TenantAdminController::class, 'storeContract']);
        Route::delete('/contracts/{id}', [TenantAdminController::class, 'deleteContract']);

        // Chốt số & Tính tiền tự động
        Route::post('/utility/record', [TenantAdminController::class, 'storeUtilityRecord']);

        // Xuất hóa đơn PDF & Gửi thông báo
        Route::get('/bills/{id}/pdf', [TenantAdminController::class, 'printPdf']);
        Route::post('/bills/{id}/notify', [TenantAdminController::class, 'notifyResident']);

        // Xử lý sự cố (Tickets)
        Route::get('/tickets', [TenantAdminController::class, 'listTickets']);
        Route::put('/tickets/{id}', [TenantAdminController::class, 'updateTicket']);
    });

    // ------------------------------------------
    // B. PHÂN HỆ CƯ DÂN NỘI BỘ (Resident)
    // ------------------------------------------
    Route::middleware('role:resident')->prefix('resident')->group(function () {
        Route::get('/dashboard', [ResidentController::class, 'dashboard']);
        Route::get('/bills', [ResidentController::class, 'bills']);
        Route::get('/bills/{id}/vietqr', [ResidentController::class, 'getBillQr']);
        Route::post('/tickets', [ResidentController::class, 'storeTicket']);
    });
});