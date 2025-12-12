<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Controllers\User\Auth\PasswordResetController;

// Public routes (Authentication)
Route::middleware(['guest:web'])->group(function () {
    // Login - attempts tracked in database with warning/redirect flow
    Route::get('login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
    
    // Registration - DB rate limiting with warnings
    Route::get('register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('send-registration-otp', [RegisterController::class, 'sendRegistrationOtp'])
        ->middleware('rate.limit:otp')
        ->name('send-registration-otp');
    Route::post('verify-otp', [RegisterController::class, 'verifyRegistrationOtp'])
        ->middleware('rate.limit:otp')
        ->name('verify-otp');
    Route::post('register', [RegisterController::class, 'register'])
        ->middleware('rate.limit:register')
        ->name('register.submit');
    
    // Password Reset - DB rate limiting with warnings
    Route::get('forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('send-password-reset-otp', [PasswordResetController::class, 'sendPasswordResetOtp'])
        ->middleware('rate.limit:otp')
        ->name('send-password-reset-otp');
    Route::post('verify-password-reset-otp', [PasswordResetController::class, 'verifyPasswordResetOtp'])
        ->middleware('rate.limit:otp')
        ->name('verify-password-reset-otp');
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('reset-password.submit');
});

// Protected routes
Route::middleware(['auth:web', 'prevent.back'])->group(function () {
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
