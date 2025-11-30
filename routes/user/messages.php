<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MessageController;

Route::middleware(['auth:web', 'prevent.back'])->group(function () {
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{adminId}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('messages/{adminId}/send', [MessageController::class, 'send'])->name('messages.send');
    Route::get('api/messages/{adminId}', [MessageController::class, 'getMessages'])->name('api.messages');
    Route::get('api/messages/unread/count', [MessageController::class, 'unreadCount'])->name('api.messages.unread');
});
