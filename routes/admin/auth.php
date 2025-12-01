<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\PasswordResetController;

// Public routes (Authentication) with rate limiting
Route::middleware(['guest:admin', 'throttle:5,1'])->group(function () {
    // Login
    Route::get('login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
    
    // Password Reset
    Route::get('forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('send-password-reset', [PasswordResetController::class, 'sendPasswordReset'])->name('send-password-reset');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetPassword'])->name('reset-password');
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])->name('reset-password.submit');
    Route::post('verify-password-reset', [PasswordResetController::class, 'verifyPasswordReset'])->name('verify-password-reset');
});

// Protected routes
Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
