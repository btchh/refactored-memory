<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_type' => $this->message->sender_type,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender_name,
            'message' => $this->message->message,
            'has_attachment' => $this->message->has_attachment,
            'attachment_url' => $this->message->attachment_url,
            'attachment_type' => $this->message->attachment_type,
            'attachment_name' => $this->message->attachment_name,
            'is_read' => $this->message->is_read,
            'created_at' => $this->message->created_at->toISOString(),
            'formatted_time' => $this->message->created_at->format('g:i A'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
