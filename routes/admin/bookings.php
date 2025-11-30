<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\BookingManagementController;

Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    // Booking Management (Status Updates)
    Route::get('bookings/manage', [BookingManagementController::class, 'index'])->name('bookings.manage');
    Route::get('bookings/{id}/details', [BookingManagementController::class, 'show'])->name('bookings.details');
    Route::post('bookings/{id}/update-status', [BookingManagementController::class, 'updateStatus'])->name('bookings.updateStatus');
    Route::post('bookings/{id}/update-weight', [BookingManagementController::class, 'updateWeight'])->name('bookings.updateWeight');
    
    // Booking Management (Calendar/Creation)
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::put('bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
    Route::post('bookings/{id}/reschedule', [BookingController::class, 'reschedule'])->name('bookings.reschedule');
    Route::delete('bookings/{id}', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');
    
    // Booking AJAX Endpoints
    Route::get('api/users/search', [BookingController::class, 'searchUsers'])->name('api.users.search');
    Route::get('api/bookings/user/{userId}', [BookingController::class, 'getUserBookings'])->name('api.bookings.user');
    Route::get('api/bookings/by-date', [BookingController::class, 'getBookingsByDate'])->name('api.bookings.by-date');
    Route::get('api/bookings/counts', [BookingController::class, 'getBookingCounts'])->name('api.bookings.counts');
    Route::get('api/calendar/slots', [BookingController::class, 'getAvailableSlots'])->name('api.calendar.slots');
    Route::post('api/bookings/calculate', [BookingController::class, 'calculateTotal'])->name('api.bookings.calculate');
});
