<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Controllers\User\Auth\PasswordResetController;

// Public routes (Authentication) with rate limiting
Route::middleware(['guest:web', 'throttle:5,1'])->group(function () {
    // Login
    Route::get('login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
    
    // Registration
    Route::get('register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('send-registration-otp', [RegisterController::class, 'sendRegistrationOtp'])->name('send-registration-otp');
    Route::post('verify-otp', [RegisterController::class, 'verifyRegistrationOtp'])->name('verify-otp');
    Route::post('register', [RegisterController::class, 'register'])->name('register.submit');
    
    // Password Reset
    Route::get('forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('send-password-reset-otp', [PasswordResetController::class, 'sendPasswordResetOtp'])->name('send-password-reset-otp');
    Route::post('verify-password-reset-otp', [PasswordResetController::class, 'verifyPasswordResetOtp'])->name('verify-password-reset-otp');
    Route::get('reset-password/{phone}', [PasswordResetController::class, 'showResetPassword'])->name('reset-password');
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])->name('reset-password.submit');
});

// Protected routes
Route::middleware(['auth:web', 'prevent.back'])->group(function () {
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
