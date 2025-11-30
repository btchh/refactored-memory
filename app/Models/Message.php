<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        if ($this->sender_type === 'user') {
            return $this->belongsTo(User::class, 'sender_id');
        }
        return $this->belongsTo(Admin::class, 'sender_id');
    }

    public function getSenderNameAttribute(): string
    {
        if ($this->sender_type === 'user') {
            $user = User::find($this->sender_id);
            return $user ? $user->fname . ' ' . $user->lname : 'Unknown User';
        }
        $admin = Admin::find($this->sender_id);
        return $admin ? $admin->fname . ' ' . $admin->lname : 'Unknown Admin';
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
