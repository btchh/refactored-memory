<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $table = 'admins';

    protected $fillable = [
        'username',
        'fname',
        'lname',
        'email',
        'phone',
        'address',
        'branch_name',
        'branch_address',
        'branch_latitude',
        'branch_longitude',
        'latitude',
        'longitude',
        'location_updated_at',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'location_updated_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get the transactions managed by this admin
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the services for this admin/branch
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the products for this admin/branch
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
