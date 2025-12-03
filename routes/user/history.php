<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\HistoryController;

Route::middleware(['auth:web', 'prevent.back', 'check.user.status'])->group(function () {
    Route::get('status', [HistoryController::class, 'showStatus'])->name('status');
    Route::get('history', [HistoryController::class, 'history'])->name('history');
});
