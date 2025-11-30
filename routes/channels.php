<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }
    
    // Check if user is part of this conversation
    // $user could be either a User or Admin model
    if (method_exists($user, 'getTable')) {
        if ($user->getTable() === 'users') {
            return $conversation->user_id === $user->id;
        } elseif ($user->getTable() === 'admins') {
            return $conversation->admin_id === $user->id;
        }
    }
    
    return false;
});
