<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;



Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (! $conversation) {
        return false;
    }

    // Chỉ cho phép user là người tham gia cuộc trò chuyện
    return (int) $user->id === (int) $conversation->user_id
        || (int) $user->id === (int) $conversation->admin_id;
});
