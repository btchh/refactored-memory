<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('landingPage');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Public routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminController::class, 'login']);
        Route::get('forgot-password', [AdminController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('send-password-reset', [AdminController::class, 'sendPasswordReset'])->name('send-password-reset');
        Route::get('reset-password/{token}', [AdminController::class, 'showResetPassword'])->name('reset-password');
        Route::post('reset-password', [AdminController::class, 'resetPassword']);
        Route::post('verify-password-reset', [AdminController::class, 'verifyPasswordReset'])->name('verify-password-reset');
    });

    // Protected routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('dashboard', [AdminController::class, 'showDashboard'])->name('dashboard');
        Route::get('profile', [AdminController::class, 'showProfile'])->name('profile');
        Route::post('update-profile', [AdminController::class, 'updateProfile'])->name('update-profile');
        Route::get('change-password', [AdminController::class, 'showChangePassword'])->name('change-password');
        Route::post('change-password', [AdminController::class, 'changePassword']);
        Route::get('create-admin', [AdminController::class, 'showCreateAdmin'])->name('create-admin');
        Route::post('create-admin', [AdminController::class, 'createAdmin']);
    });
});

// User Routes
Route::prefix('user')->name('user.')->group(function () {
    // Public routes
    Route::middleware('guest:web')->group(function () {
        Route::get('login', [UserController::class, 'showLogin'])->name('login');
        Route::post('login', [UserController::class, 'login']);
        Route::get('register', [UserController::class, 'showRegister'])->name('register');
        Route::post('send-registration-otp', [UserController::class, 'sendRegistrationOtp'])->name('send-registration-otp');
        Route::post('register', [UserController::class, 'register']);
        Route::get('forgot-password', [UserController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('send-password-reset-otp', [UserController::class, 'sendPasswordResetOtp'])->name('send-password-reset-otp');
        Route::post('verify-password-reset-otp', [UserController::class, 'verifyPasswordResetOtp'])->name('verify-password-reset-otp');
        Route::get('reset-password/{phone}', [UserController::class, 'showResetPassword'])->name('reset-password');
        Route::post('reset-password', [UserController::class, 'resetPassword']);
    });

    // Protected routes
    Route::middleware('auth:web')->group(function () {
        Route::post('logout', [UserController::class, 'logout'])->name('logout');
        Route::get('dashboard', [UserController::class, 'showDashboard'])->name('dashboard');
        Route::get('profile', [UserController::class, 'showProfile'])->name('profile');
        Route::post('update-profile', [UserController::class, 'updateProfile'])->name('update-profile');
        Route::get('change-password', [UserController::class, 'showChangePassword'])->name('change-password');
        Route::post('change-password', [UserController::class, 'changePassword']);
    });
});

// Alias for users.dashboard used in AdminController
Route::get('/users/dashboard', [UserController::class, 'showDashboard'])->name('users.dashboard')->middleware('auth:web');
