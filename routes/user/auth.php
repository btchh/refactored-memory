<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Controllers\User\Auth\PasswordResetController;

// Public routes (Authentication) with progressive rate limiting
Route::middleware(['guest:web'])->group(function () {
    // Login - Progressive rate limiting (5 attempts, then 5min, 10min, 15min...)
    Route::get('login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login'])
        ->middleware('rate.limit.progressive:login')
        ->name('login.submit');
    
    // Registration - Progressive rate limiting (3 attempts for OTP, 3 for registration)
    Route::get('register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('send-registration-otp', [RegisterController::class, 'sendRegistrationOtp'])
        ->middleware('rate.limit.progressive:otp')
        ->name('send-registration-otp');
    Route::post('verify-otp', [RegisterController::class, 'verifyRegistrationOtp'])
        ->middleware('rate.limit.progressive:otp')
        ->name('verify-otp');
    Route::post('register', [RegisterController::class, 'register'])
        ->middleware('rate.limit.progressive:register')
        ->name('register.submit');
    
    // Password Reset - Rate limiting disabled for testing
    Route::get('forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('send-password-reset-otp', [PasswordResetController::class, 'sendPasswordResetOtp'])
        // ->middleware('rate.limit.progressive:otp')
        ->name('send-password-reset-otp');
    Route::post('verify-password-reset-otp', [PasswordResetController::class, 'verifyPasswordResetOtp'])
        // ->middleware('rate.limit.progressive:otp')
        ->name('verify-password-reset-otp');
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])
        // ->middleware('rate.limit.progressive:login')
        ->name('reset-password.submit');
});

// Protected routes
Route::middleware(['auth:web', 'prevent.back'])->group(function () {
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
