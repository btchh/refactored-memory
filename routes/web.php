<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Landing Page
Route::get('/', function () {
    return view('landingPage');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/admin/auth.php';
    require __DIR__.'/admin/dashboard.php';
    require __DIR__.'/admin/profile.php';
    require __DIR__.'/admin/users.php';
    require __DIR__.'/admin/bookings.php';
    require __DIR__.'/admin/pricing.php';
    require __DIR__.'/admin/analytics.php';
    require __DIR__.'/admin/revenue.php';
    require __DIR__.'/admin/delivery.php';
    require __DIR__.'/admin/admin-management.php';
    require __DIR__.'/admin/messages.php';
});

// User Routes
Route::prefix('user')->name('user.')->group(function () {
    require __DIR__.'/user/auth.php';
    require __DIR__.'/user/dashboard.php';
    require __DIR__.'/user/profile.php';
    require __DIR__.'/user/bookings.php';
    require __DIR__.'/user/location.php';
    require __DIR__.'/user/history.php';
    require __DIR__.'/user/messages.php';
});
