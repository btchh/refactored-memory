<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\PasswordResetController;

// Public routes (Authentication) with progressive rate limiting
Route::middleware(['guest:admin'])->group(function () {
    // Login - Progressive rate limiting (5 attempts, then 5min, 10min, 15min...)
    Route::get('login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login'])
        ->middleware('rate.limit.progressive:login')
        ->name('login.submit');
    
    // Password Reset - Progressive rate limiting (3 attempts for OTP)
    Route::get('forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('send-password-reset', [PasswordResetController::class, 'sendPasswordReset'])
        ->middleware('rate.limit.progressive:otp')
        ->name('send-password-reset');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetPassword'])->name('reset-password');
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])
        ->middleware('rate.limit.progressive:login')
        ->name('reset-password.submit');
    Route::post('verify-password-reset', [PasswordResetController::class, 'verifyPasswordReset'])
        ->middleware('rate.limit.progressive:otp')
        ->name('verify-password-reset');
});

// Protected routes
Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
