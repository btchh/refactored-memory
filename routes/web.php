<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\User\HistoryController;

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
    Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
        Route::post('logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('dashboard', [AdminController::class, 'showDashboard'])->name('dashboard');
        Route::get('profile', [AdminController::class, 'showProfile'])->name('profile');
        Route::post('update-profile', [AdminController::class, 'updateProfile'])->name('update-profile');
        Route::get('change-password', [AdminController::class, 'showChangePassword'])->name('change-password');
        Route::post('change-password', [AdminController::class, 'changePassword']);
        Route::get('create-admin', [AdminController::class, 'showCreateAdmin'])->name('create-admin');
        Route::post('create-admin', [AdminController::class, 'createAdmin']);
        Route::post('send-admin-otp', [AdminController::class, 'sendAdminOtp'])->name('send-admin-otp');
        Route::post('verify-admin-otp', [AdminController::class, 'verifyAdminOtp'])->name('verify-admin-otp');
        Route::get('route-to-user', [AdminController::class, 'showRouteToUser'])->name('route-to-user');
        Route::get('get-route/{userId}', [AdminController::class, 'getRouteToUser'])->name('get-route');
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
        Route::post('verify-otp', [UserController::class, 'verifyRegistrationOtp'])->name('verify-otp');
        Route::post('register', [UserController::class, 'register']);
        Route::get('forgot-password', [UserController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('send-password-reset-otp', [UserController::class, 'sendPasswordResetOtp'])->name('send-password-reset-otp');
        Route::post('verify-password-reset-otp', [UserController::class, 'verifyPasswordResetOtp'])->name('verify-password-reset-otp');
        Route::get('reset-password/{phone}', [UserController::class, 'showResetPassword'])->name('reset-password');
        Route::post('reset-password', [UserController::class, 'resetPassword']);
    });
   
    
    // Protected routes
    Route::middleware(['auth:web', 'prevent.back'])->group(function () {
        Route::post('logout', [UserController::class, 'logout'])->name('logout');
        Route::get('dashboard', [UserController::class, 'showDashboard'])->name('dashboard');
        Route::get('booking', [UserController::class, 'showBooking'])->name('booking');
        Route::post('booking', [UserController::class, 'submitBooking'])->name('booking.submit');
        Route::get('status', [UserController::class, 'showStatus'])->name('status');
        Route::get('shop-location', [UserController::class, 'shopLocation'])->name('shop-location');
        Route::get('history', [UserController::class, 'history'])->name('history');
        Route::get('profile', [UserController::class, 'showProfile'])->name('profile');
        Route::post('update-profile', [UserController::class, 'updateProfile'])->name('update-profile');
        Route::get('change-password', [UserController::class, 'showChangePassword'])->name('change-password');
        Route::post('change-password', [UserController::class, 'changePassword']);
        Route::get('track-admin', [UserController::class, 'showTrackAdmin'])->name('track-admin');
        Route::get('admin-location', [UserController::class, 'getAdminLocation'])->name('admin-location');
    });
});

// API routes
Route::get('/api/users', function() {
    $users = \App\Models\User::select('id', 'username', 'fname', 'lname', 'phone', 'address')
        ->get()
        ->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->fname . ' ' . $user->lname,
                'phone' => $user->phone,
                'address' => $user->address
            ];
        });
    
    return response()->json(['success' => true, 'users' => $users]);
})->middleware('auth:admin');
