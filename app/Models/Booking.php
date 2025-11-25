<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'calcom_booking_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'status',
        'attendee_email',
        'attendee_name',
        'attendee_phone',
        'location',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include upcoming bookings.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_time', '>', now())
                    ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope a query to only include past bookings.
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('end_time', '<', now());
    }
}
