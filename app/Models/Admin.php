<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'admin_name',
        'fname',
        'lname',
        'address',
        'phone',
        'email',
        'password',
        'latitude',
        'longitude',
        'location_updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'location_updated_at' => 'datetime',
        'password' => 'hashed',
    ];
}
