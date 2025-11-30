<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RouteController;

Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    // Delivery tracking - route to users
    Route::get('route-to-user', [RouteController::class, 'showRouteToUser'])->name('route-to-user');
    Route::get('api/users', [RouteController::class, 'getUsersWithLocation'])->name('api.users');
    Route::get('get-route/{userId}', [RouteController::class, 'getRouteToUser'])->name('get-route');
});
