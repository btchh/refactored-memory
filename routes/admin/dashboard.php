<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
});
