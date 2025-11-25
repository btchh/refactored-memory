<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\RouteController as AdminRouteController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\TrackingController as UserTrackingController;
use App\Http\Controllers\User\BookingController as UserBookingController;

Route::get('/', function () {
    return view('landingPage');
});

// ============================================================================
// Admin Routes
// ============================================================================
Route::prefix('admin')->name('admin.')->group(function () {
    
    // ------------------------------------------------------------------------
    // Admin Authentication Routes (Public)
    // ------------------------------------------------------------------------
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::get('forgot-password', [AdminAuthController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('send-password-reset', [AdminAuthController::class, 'sendPasswordReset'])->name('send-password-reset');
        Route::post('verify-password-reset', [AdminAuthController::class, 'verifyPasswordReset'])->name('verify-password-reset');
        Route::post('reset-password', [AdminAuthController::class, 'resetPassword'])->name('reset-password');
    });

    // ------------------------------------------------------------------------
    // Admin Protected Routes
    // ------------------------------------------------------------------------
    Route::middleware(['isAdmin', 'prevent.back'])->group(function () {
        
        // Authentication
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('dashboard', [AdminProfileController::class, 'showDashboard'])->name('dashboard');
        
        // Profile Management
        Route::get('profile', [AdminProfileController::class, 'showProfile'])->name('profile');
        Route::post('update-profile', [AdminProfileController::class, 'updateProfile'])->name('update-profile');
        Route::get('change-password', [AdminProfileController::class, 'showChangePassword'])->name('change-password');
        Route::post('change-password', [AdminProfileController::class, 'changePassword'])->name('update-password');
        
        // Admin Management
        Route::get('create-admin', [AdminManagementController::class, 'showCreateAdmin'])->name('create-admin');
        Route::post('create-admin', [AdminManagementController::class, 'createAdmin']);
        Route::post('send-admin-otp', [AdminManagementController::class, 'sendAdminOtp'])->name('send-admin-otp');
        Route::post('verify-admin-otp', [AdminManagementController::class, 'verifyAdminOtp'])->name('verify-admin-otp');
        
        // Routing/Tracking
        Route::get('route-to-user', [AdminRouteController::class, 'showRouteToUser'])->name('route-to-user');
        Route::get('get-route/{userId}', [AdminRouteController::class, 'getRouteToUser'])->name('get-route');
        
        // Booking Management
        Route::get('bookings', [AdminBookingController::class, 'viewAllBookings'])->name('bookings');
        Route::get('bookings/data', [AdminBookingController::class, 'getAllBookingsData'])->name('bookings.data');
        Route::post('bookings/{bookingId}/manage', [AdminBookingController::class, 'manageBooking'])->name('bookings.manage');
        Route::post('bookings/{bookingId}/cancel', [AdminBookingController::class, 'cancelUserBooking'])->name('bookings.cancel');
        Route::get('bookings/search', [AdminBookingController::class, 'searchBookings'])->name('bookings.search');
    });
});

// ============================================================================
// User Routes
// ============================================================================
Route::prefix('user')->name('user.')->group(function () {
    
    // ------------------------------------------------------------------------
    // User Authentication Routes (Public)
    // ------------------------------------------------------------------------
    Route::middleware('guest:web')->group(function () {
        Route::get('login', [UserAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [UserAuthController::class, 'login']);
        Route::get('register', [UserAuthController::class, 'showRegister'])->name('register');
        Route::post('send-registration-otp', [UserAuthController::class, 'sendRegistrationOtp'])->name('send-registration-otp');
        Route::post('verify-otp', [UserAuthController::class, 'verifyRegistrationOtp'])->name('verify-otp');
        Route::post('register', [UserAuthController::class, 'register']);
        Route::get('forgot-password', [UserAuthController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('send-password-reset-otp', [UserAuthController::class, 'sendPasswordResetOtp'])->name('send-password-reset-otp');
        Route::post('verify-password-reset-otp', [UserAuthController::class, 'verifyPasswordResetOtp'])->name('verify-password-reset-otp');
        Route::post('reset-password', [UserAuthController::class, 'resetPassword'])->name('reset-password');
    });

    // ------------------------------------------------------------------------
    // User Protected Routes
    // ------------------------------------------------------------------------
    Route::middleware(['isUser', 'prevent.back'])->group(function () {
        
        // Authentication
        Route::post('logout', [UserAuthController::class, 'logout'])->name('logout');
        
        // Dashboard
        Route::get('dashboard', [UserProfileController::class, 'showDashboard'])->name('dashboard');
        
        // Profile Management
        Route::get('profile', [UserProfileController::class, 'showProfile'])->name('profile');
        Route::post('update-profile', [UserProfileController::class, 'updateProfile'])->name('update-profile');
        Route::get('change-password', [UserProfileController::class, 'showChangePassword'])->name('change-password');
        Route::post('change-password', [UserProfileController::class, 'changePassword']);
        
        // Tracking
        Route::get('track-admin', [UserTrackingController::class, 'showTrackAdmin'])->name('track-admin');
        Route::get('admin-location', [UserTrackingController::class, 'getAdminLocation'])->name('admin-location');
        
        // Booking Management
        Route::get('bookings', [UserBookingController::class, 'viewBookings'])->name('bookings');
        Route::get('bookings/data', [UserBookingController::class, 'getUserBookingsData'])->name('bookings.data');
        Route::post('bookings/create', [UserBookingController::class, 'createBooking'])->name('bookings.create');
        Route::post('bookings/{bookingId}/update', [UserBookingController::class, 'updateBooking'])->name('bookings.update');
        Route::post('bookings/{bookingId}/cancel', [UserBookingController::class, 'cancelBooking'])->name('bookings.cancel');
    });
});

// ============================================================================
// API Routes
// ============================================================================
Route::get('/api/users', [AdminRouteController::class, 'getUsersList'])->middleware('isAdmin');
