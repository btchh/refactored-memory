<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\LocationController;

Route::middleware(['auth:web', 'prevent.back', 'check.user.status'])->group(function () {
    Route::get('route-to-admin', [LocationController::class, 'showRouteToAdmin'])->name('route-to-admin');
    Route::get('api/admins', [LocationController::class, 'getAdminsWithLocation'])->name('api.admins');
});
