<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'user_id',
        'branch_address',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all admins for this branch
     */
    public function branchAdmins()
    {
        return Admin::where('branch_address', $this->branch_address)->get();
    }

    /**
     * Get the first admin for this branch (for display purposes)
     */
    public function getFirstBranchAdminAttribute()
    {
        return Admin::where('branch_address', $this->branch_address)->first();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function unreadCountForUser(): int
    {
        return $this->messages()
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->count();
    }

    public function unreadCountForAdmin(): int
    {
        return $this->messages()
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->count();
    }

    public static function findOrCreateForBranch(int $userId, string $branchAddress): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'branch_address' => $branchAddress]
        );
    }

    /**
     * Get all unique branch addresses
     */
    public static function getAllBranches(): array
    {
        return Admin::distinct()->pluck('branch_address')->filter()->toArray();
    }
}
