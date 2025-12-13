<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $conversationId,
        public string $senderType,
        public string $senderName,
        public bool $isTyping = true
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->conversationId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'sender_type' => $this->senderType,
            'sender_name' => $this->senderName,
            'is_typing' => $this->isTyping,
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.typing';
    }
}
