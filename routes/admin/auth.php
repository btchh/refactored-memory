<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\PasswordResetController;

// Public routes (Authentication)
Route::middleware(['guest:admin'])->group(function () {
    // Login - attempts tracked in database with warning/redirect flow
    Route::get('login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
    
    // Password Reset - DB rate limiting with warnings
    Route::get('forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('send-password-reset-otp', [PasswordResetController::class, 'sendPasswordReset'])
        ->middleware('rate.limit:otp')
        ->name('send-password-reset-otp');
    Route::post('verify-password-reset-otp', [PasswordResetController::class, 'verifyPasswordReset'])
        ->middleware('rate.limit:otp')
        ->name('verify-password-reset-otp');
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('reset-password.submit');
});

// Protected routes
Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
