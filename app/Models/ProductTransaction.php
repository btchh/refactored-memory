<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductTransaction extends Pivot
{
    protected $table = 'product_transactions';

    protected $fillable = [
        'transaction_id',
        'product_id',
        'price_at_purchase',
    ];

    protected $casts = [
        'price_at_purchase' => 'decimal:2',
    ];

    public $incrementing = true;
}
