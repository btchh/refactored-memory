<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MessageController;

Route::middleware(['auth:web', 'prevent.back', 'check.user.status'])->group(function () {
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/branch/{branchAddress}', [MessageController::class, 'show'])->name('messages.show')->where('branchAddress', '.*');
    Route::post('messages/branch/{branchAddress}/send', [MessageController::class, 'send'])->name('messages.send')->where('branchAddress', '.*');
    Route::post('messages/branch/{branchAddress}/typing', [MessageController::class, 'typing'])->name('messages.typing')->where('branchAddress', '.*');
    Route::get('api/messages/branch/{branchAddress}', [MessageController::class, 'getMessages'])->name('api.messages')->where('branchAddress', '.*');
    Route::get('api/messages/unread/count', [MessageController::class, 'unreadCount'])->name('api.messages.unread');
});
