<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuditController;

Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    Route::get('audit', [AuditController::class, 'index'])->name('audit');
    Route::get('api/audit', [AuditController::class, 'getLogs'])->name('api.audit');
});
