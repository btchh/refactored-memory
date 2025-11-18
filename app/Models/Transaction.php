<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transaction extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'weight',
        'total_price',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($transaction) {
            $transaction->calculateTotalPrice();
        });
    }

    public function calculateTotalPrice()
    {
        $productTotal = $this->products()
            ->get()
            ->sum('pivot.price_at_purchase');

        $serviceTotal = $this->services()
            ->get()
            ->sum('pivot.price_at_purchase');

        $this->total_price = $productTotal + $serviceTotal;
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_transactions')
            ->using(ProductTransaction::class)
            ->withPivot('price_at_purchase')
            ->withTimestamps();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_transactions')
            ->using(ServiceTransaction::class)
            ->withPivot('price_at_purchase')
            ->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
