<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'time',
        'status',
        'services',
        'total',
        'notes'
    ];

    protected $casts = [
        'services' => 'array',
        'date' => 'date',
        'total' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
