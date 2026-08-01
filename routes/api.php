<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\HotelController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Version 1
|--------------------------------------------------------------------------
| Prefix: /api/v1
| Auth: Laravel Sanctum (Bearer Token)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Public Routes ────────────────────────────────────

    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Hotels (public)
    Route::get('/hotels', [HotelController::class, 'index']);
    Route::get('/hotels/cities', [HotelController::class, 'cities']);
    Route::get('/hotels/{slug}', [HotelController::class, 'show']);

    // Rooms (public)
    Route::get('/hotels/{hotelId}/rooms', [RoomController::class, 'index']);
    Route::get('/hotels/{hotelId}/rooms/{roomId}', [RoomController::class, 'show']);
    Route::post('/rooms/available', [RoomController::class, 'available']);

    // Reviews (public, read-only)
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/reviews/{review}', [ReviewController::class, 'show']);

    // ── Protected Routes (Sanctum) ───────────────────────

    Route::middleware('auth:sanctum')->group(function () {

        // Auth / Profile
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateProfile']);
        Route::delete('/me', [AuthController::class, 'destroy']);

        // Bookings
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/bookings/{reservation}', [BookingController::class, 'show']);
        Route::post('/bookings/{reservation}/cancel', [BookingController::class, 'cancel']);

        // Payments
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::get('/payments/{payment}', [PaymentController::class, 'show']);

        // Reviews (write)
        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    });
});
