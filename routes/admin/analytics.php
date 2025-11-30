<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AnalyticsController;

Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});
