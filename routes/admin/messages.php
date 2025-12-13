<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MessageController;

Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{userId}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('messages/{userId}/send', [MessageController::class, 'send'])->name('messages.send');
    Route::post('messages/{userId}/typing', [MessageController::class, 'typing'])->name('messages.typing');
    Route::get('api/messages/{userId}', [MessageController::class, 'getMessages'])->name('api.messages');
    Route::get('api/messages/unread/count', [MessageController::class, 'unreadCount'])->name('api.messages.unread');
});
