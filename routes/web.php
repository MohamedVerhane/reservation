<?php

use App\Http\Controllers\Admin\AmenityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Frontend\ReviewController as FrontendReviewController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'ar', 'fr'], true), 404);

    session(['locale' => $locale]);

    return redirect()->back();
})->name('language.switch');

// ── Frontend Routes ───────────────────────────────────────
Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/hotels', [FrontendController::class, 'hotels'])->name('frontend.hotels');
Route::get('/hotels/{slug}', [FrontendController::class, 'hotelShow'])->name('frontend.hotel.show');
Route::get('/hotels/{hotelId}/rooms/{roomId}', [FrontendController::class, 'roomShow'])->name('frontend.room.show');
Route::get('/about', [FrontendController::class, 'about'])->name('frontend.about');
Route::get('/contact', [FrontendController::class, 'contact'])->name('frontend.contact');
Route::post('/contact', [FrontendController::class, 'sendContact'])->name('contact.send');
Route::get('/gallery', [FrontendController::class, 'gallery'])->name('frontend.gallery');

// ── Advanced Search Routes ───────────────────────────────
Route::get('/search', [SearchController::class, 'index'])->name('frontend.search');
Route::get('/search/ajax', [SearchController::class, 'ajax'])->name('frontend.search.ajax');
Route::get('/search/live', [SearchController::class, 'live'])->name('frontend.search.live');
Route::get('/search/options', [SearchController::class, 'options'])->name('frontend.search.options');

// ── Frontend Review Routes ───────────────────────────────
Route::post('/hotels/{hotelSlug}/reviews', [FrontendReviewController::class, 'store'])->name('frontend.hotel.reviews.store')->middleware(['auth', 'verified']);
Route::delete('/hotels/{hotelSlug}/reviews/{review}', [FrontendReviewController::class, 'destroy'])->name('frontend.hotel.reviews.destroy')->middleware(['auth', 'verified']);

// ── Booking Routes ────────────────────────────────────────
Route::middleware(['auth', 'verified', 'throttle:60,1'])->group(function () {
    Route::get('/book/{hotelSlug}', [BookingController::class, 'show'])->name('frontend.booking.show');
    Route::post('/book/check-availability', [BookingController::class, 'checkAvailability'])->name('frontend.booking.check-availability');
    Route::get('/book/{hotelId}/calendar', [BookingController::class, 'calendar'])->name('frontend.booking.calendar');
    Route::post('/book/select-room', [BookingController::class, 'selectRoom'])->name('frontend.booking.select-room');
    Route::post('/book/review-booking', [BookingController::class, 'review'])->name('frontend.booking.review');
    Route::get('/book/review', [BookingController::class, 'reviewForm'])->name('frontend.booking.review-form');
    Route::post('/book/confirm', [BookingController::class, 'store'])->name('frontend.booking.store');
    Route::get('/booking/{reservation}/confirmation', [BookingController::class, 'confirmation'])->name('frontend.booking.confirmation');
    Route::get('/my-bookings', [BookingController::class, 'myReservations'])->name('frontend.booking.my-reservations');
});

// ── Notification Routes (shared auth) ────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// ── Profile Redirect ─────────────────────────────────────
Route::redirect('/profile', '/my-dashboard/profile');

// ── Customer Dashboard Routes ────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('my-dashboard')->name('customer.')->group(function () {
    Route::get('/', [App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reservations', [App\Http\Controllers\Customer\DashboardController::class, 'reservations'])->name('reservations');
    Route::post('/reservations/{reservation}/cancel', [App\Http\Controllers\Customer\DashboardController::class, 'cancelReservation'])->name('reservations.cancel');
    Route::get('/profile', [App\Http\Controllers\Customer\DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Customer\DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Customer\DashboardController::class, 'updatePassword'])->name('profile.password');
    Route::get('/reviews', [App\Http\Controllers\Customer\DashboardController::class, 'reviews'])->name('reviews');
    Route::delete('/reviews/{review}', [App\Http\Controllers\Customer\DashboardController::class, 'destroyReview'])->name('reviews.destroy');
    Route::get('/favorites', [App\Http\Controllers\Customer\DashboardController::class, 'favorites'])->name('favorites');
    Route::post('/favorites/toggle', [App\Http\Controllers\Customer\DashboardController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::get('/invoices', [App\Http\Controllers\Customer\DashboardController::class, 'invoices'])->name('invoices');
    Route::get('/history', [App\Http\Controllers\Customer\DashboardController::class, 'history'])->name('history');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
});

// ── Admin Routes ──────────────────────────────────────────
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/notifications', [NotificationController::class, 'adminIndex'])->name('notifications');

    // Hotel CRUD
    Route::patch('/hotels/{hotel}/toggle', [HotelController::class, 'toggleStatus'])->name('hotels.toggle');
    Route::patch('/hotels/{id}/restore', [HotelController::class, 'restore'])->name('hotels.restore');
    Route::delete('/hotels/{id}/force-delete', [HotelController::class, 'forceDelete'])->name('hotels.force-delete');
    Route::resource('hotels', HotelController::class);

    // Room Type CRUD
    Route::patch('/room-types/{roomType}/toggle', [RoomTypeController::class, 'toggleStatus'])->name('room-types.toggle');
    Route::resource('room-types', RoomTypeController::class);

    // Room CRUD
    Route::patch('/rooms/{room}/toggle', [RoomController::class, 'toggleStatus'])->name('rooms.toggle');
    Route::post('/rooms/{room}/images', [RoomController::class, 'uploadImage'])->name('rooms.images.upload');
    Route::patch('/rooms/{room}/images/{image}/primary', [RoomController::class, 'setPrimary'])->name('rooms.images.primary');
    Route::delete('/rooms/{room}/images/{image}', [RoomController::class, 'deleteImage'])->name('rooms.images.delete');
    Route::resource('rooms', RoomController::class);

    // Reservation CRUD
    Route::patch('/reservations/{reservation}/toggle', [ReservationController::class, 'toggleStatus'])->name('reservations.toggle');
    Route::post('/reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::post('/reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.check-in');
    Route::post('/reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.check-out');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::get('/reservations/hotels/{hotel}/rooms', [ReservationController::class, 'getRooms'])->name('reservations.rooms');
    Route::get('/reservations/calculate-price', [ReservationController::class, 'calculatePrice'])->name('reservations.calculate-price');
    Route::resource('reservations', ReservationController::class);

    // Review CRUD
    Route::patch('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
    Route::patch('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('/reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
    Route::patch('/reviews/{id}/restore', [ReviewController::class, 'restore'])->name('reviews.restore');
    Route::resource('reviews', ReviewController::class)->except(['create', 'edit', 'update']);

    // Gallery CRUD
    Route::post('/galleries/{gallery}/images', [GalleryController::class, 'uploadImages'])->name('galleries.images.upload');
    Route::delete('/galleries/{gallery}/images/{image}', [GalleryController::class, 'deleteImage'])->name('galleries.images.delete');
    Route::resource('galleries', GalleryController::class);

    // Payment CRUD
    Route::patch('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.update-status');
    Route::resource('payments', PaymentController::class)->except(['edit', 'update']);

    // User CRUD
    Route::patch('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::resource('users', UserController::class);

    // Amenity CRUD
    Route::patch('/amenities/{amenity}/toggle', [AmenityController::class, 'toggleStatus'])->name('amenities.toggle');
    Route::get('/amenities/{amenity}/manage-rooms', [AmenityController::class, 'manageRooms'])->name('amenities.manage-rooms');
    Route::post('/amenities/{amenity}/assign-rooms', [AmenityController::class, 'assignRooms'])->name('amenities.assign-rooms');
    Route::get('/amenities/rooms/ajax', [AmenityController::class, 'getRooms'])->name('amenities.rooms.ajax');
    Route::resource('amenities', AmenityController::class);
});

if (app()->environment('production')) {
    Route::middleware('cache.headers:public;max_age=2628000;etag')->group(function () {
        //
    });
}
