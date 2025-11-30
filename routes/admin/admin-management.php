<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCreationController;

Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    // Admin Creation
    Route::get('create-admin', [AdminCreationController::class, 'showCreateAdmin'])->name('create-admin.show');
    Route::post('create-admin', [AdminCreationController::class, 'store'])->name('create-admin');
    Route::post('send-admin-otp', [AdminCreationController::class, 'sendOtp'])->name('send-admin-otp');
    Route::post('verify-admin-otp', [AdminCreationController::class, 'verifyOtp'])->name('verify-admin-otp');
});
