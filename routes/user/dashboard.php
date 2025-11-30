<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;

Route::middleware(['auth:web', 'prevent.back'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
});
