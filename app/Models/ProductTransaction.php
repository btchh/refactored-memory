<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductTransaction extends Pivot
{
    protected $table = 'product_transactions';

    protected $fillable = [
        'transaction_id',
        'product_id',
        'price_at_purchase'
    ];
}
