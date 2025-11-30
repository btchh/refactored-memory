<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RevenueController;

Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    Route::get('revenue', [RevenueController::class, 'index'])->name('revenue.index');
    Route::get('revenue/export', [RevenueController::class, 'export'])->name('revenue.export');
});
