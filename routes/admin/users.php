<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManagementController;

Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('users/{id}', [UserManagementController::class, 'show'])->name('users.show');
    Route::post('users/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{id}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
    Route::post('users/{id}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
});
