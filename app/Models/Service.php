<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

class Service extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'services';

    protected $fillable = [
        'service_name',
        'price'
    ];

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'service_transactions')
            ->using(ServiceTransaction::class)
            ->withPivot('price_at_purchase')
            ->withTimestamps();
    }
}
