<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\BookingController;

Route::middleware(['auth:web', 'prevent.back', 'check.user.status'])->group(function () {
    // Booking - Progressive rate limiting (10 attempts, then 5min, 10min, 15min...)
    Route::get('booking', [BookingController::class, 'showBooking'])->name('booking');
    Route::post('booking', [BookingController::class, 'submitBooking'])
        ->middleware('rate.limit.progressive:booking')
        ->name('booking.submit');
    
    // Booking AJAX Endpoints
    Route::get('api/calendar/slots', [BookingController::class, 'getAvailableSlots'])->name('api.calendar.slots');
    Route::get('api/bookings', [BookingController::class, 'getMyBookings'])->name('api.bookings');
    Route::post('api/bookings/calculate', [BookingController::class, 'calculateTotal'])->name('api.bookings.calculate');
    Route::get('api/branch/pricing', [BookingController::class, 'getBranchPricing'])->name('api.branch.pricing');
});
