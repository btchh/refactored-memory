<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'service_name',
        'price',
        'item_type',
        'description',
        'is_bundle',
        'bundle_items'
    ];

    protected $casts = [
        'bundle_items' => 'array',
    ];

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'service_transactions')
            ->using(ServiceTransaction::class)
            ->withPivot('price_at_purchase')
            ->withTimestamps();
    }
}
