<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'fname',
        'lname',
        'address',
        'phone',
        'email',
        'password',
        'latitude',
        'longitude',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get upcoming bookings (alias for transactions)
     */
    public function upcomingBookings()
    {
        return $this->transactions()->upcoming();
    }

    /**
     * Get past bookings (alias for transactions)
     */
    public function pastBookings()
    {
        return $this->transactions()->past();
    }
}
