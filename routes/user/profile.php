<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ProfileController;

Route::middleware(['auth:web', 'prevent.back'])->group(function () {
    Route::get('profile', [ProfileController::class, 'showProfile'])->name('profile');
    Route::post('update-profile', [ProfileController::class, 'updateProfile'])->name('update-profile');
    Route::get('change-password', [ProfileController::class, 'showChangePassword'])->name('change-password');
    Route::post('change-password', [ProfileController::class, 'changePassword'])->name('change-password.submit');
});
