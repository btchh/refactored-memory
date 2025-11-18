<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ServiceTransaction extends Pivot
{
    protected $table = 'service_transactions';

    protected $fillable = [
        'transaction_id',
        'service_id',
        'price_at_purchase'
    ];
}
