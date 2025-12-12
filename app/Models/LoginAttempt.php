<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'identifier',
        'ip_address',
        'action_type',
        'failed_attempts',
        'last_attempt_at',
    ];

    protected $casts = [
        'last_attempt_at' => 'datetime',
    ];

    /**
     * Get or create an attempt record for the given identifier, IP, and action type.
     */
    public static function findOrCreateFor(string $identifier, string $ipAddress, string $actionType = 'login'): self
    {
        return self::firstOrCreate(
            [
                'identifier' => strtolower($identifier),
                'ip_address' => $ipAddress,
                'action_type' => $actionType,
            ],
            ['failed_attempts' => 0]
        );
    }

    /**
     * Reset attempts after successful action.
     */
    public function resetAttempts(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'last_attempt_at' => null,
        ]);
    }

    /**
     * Increment failed attempts.
     */
    public function incrementAttempts(): void
    {
        $this->increment('failed_attempts');
        $this->update(['last_attempt_at' => now()]);
    }

    /**
     * Check if attempts should be reset (after configured hours of inactivity).
     */
    public function shouldResetAttempts(int $hours = 1): bool
    {
        if (!$this->last_attempt_at) {
            return false;
        }
        
        return $this->last_attempt_at->diffInHours(now()) >= $hours;
    }
}
