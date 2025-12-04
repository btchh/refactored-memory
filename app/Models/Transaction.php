<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'admin_id',
        'booking_date',
        'booking_time',
        'pickup_address',
        'latitude',
        'longitude',
        'item_type',
        'pickup_method',
        'delivery_method',
        'notes',
        'calapi_event_id',
        'weight',
        'total_price',
        'status',
        'completed_at',
        'booking_type',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'completed_at' => 'datetime',
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

    /**
     * Get the admin who created/manages the transaction
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Scope for active bookings (not completed, or completed within last hour)
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('status', ['pending', 'in_progress', 'out_for_delivery'])
              ->orWhere(function ($q2) {
                  $q2->where('status', 'completed')
                     ->where('completed_at', '>=', now()->subHour());
              });
        });
    }

    /**
     * Scope for upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString())
            ->orderBy('booking_date')
            ->orderBy('booking_time');
    }

    /**
     * Scope for past bookings
     */
    public function scopePast($query)
    {
        return $query->where('booking_date', '<', now()->toDateString())
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc');
    }

    /**
     * Scope for bookings by date
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('booking_date', $date);
    }

    /**
     * Get formatted date and time
     */
    public function getFormattedDateTimeAttribute()
    {
        return $this->booking_date->format('M d, Y') . ' at ' . \Carbon\Carbon::parse($this->booking_time)->format('g:i A');
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->booking_date->format('F d, Y');
    }

    /**
     * Get formatted time
     */
    public function getFormattedTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->booking_time)->format('g:i A');
    }

    /**
     * Check if booking is upcoming
     */
    public function isUpcoming()
    {
        $bookingDateTime = \Carbon\Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->booking_time);
        return $bookingDateTime->isFuture();
    }

    /**
     * Check if booking is today
     */
    public function isToday()
    {
        return $this->booking_date->isToday();
    }

    /**
     * Check if branch handles pickup
     */
    public function isBranchPickup()
    {
        return $this->pickup_method === 'branch_pickup';
    }

    /**
     * Check if customer drops off
     */
    public function isCustomerDropoff()
    {
        return $this->pickup_method === 'customer_dropoff';
    }

    /**
     * Check if branch handles delivery
     */
    public function isBranchDelivery()
    {
        return $this->delivery_method === 'branch_delivery';
    }

    /**
     * Check if customer picks up
     */
    public function isCustomerPickup()
    {
        return $this->delivery_method === 'customer_pickup';
    }

    /**
     * Get service description based on pickup and delivery methods
     */
    public function getServiceDescriptionAttribute()
    {
        // Full Service: branch_pickup + branch_delivery
        if ($this->pickup_method === 'branch_pickup' && $this->delivery_method === 'branch_delivery') {
            return 'Full Service';
        }
        
        // Self Drop-off: customer_dropoff + branch_delivery
        if ($this->pickup_method === 'customer_dropoff' && $this->delivery_method === 'branch_delivery') {
            return 'Self Drop-off';
        }
        
        // Self Pickup: branch_pickup + customer_pickup
        if ($this->pickup_method === 'branch_pickup' && $this->delivery_method === 'customer_pickup') {
            return 'Self Pickup';
        }
        
        // Self Service: customer_dropoff + customer_pickup
        if ($this->pickup_method === 'customer_dropoff' && $this->delivery_method === 'customer_pickup') {
            return 'Self Service';
        }
        
        // Fallback for any other combination
        $pickup = $this->isBranchPickup() ? 'Branch Pickup' : 'Customer Drop-off';
        $delivery = $this->isBranchDelivery() ? 'Branch Delivery' : 'Customer Pickup';
        return "{$pickup} & {$delivery}";
    }

    /**
     * Check if full service (branch handles both)
     */
    public function isFullService()
    {
        return $this->isBranchPickup() && $this->isBranchDelivery();
    }

    /**
     * Check if self service (customer handles both)
     */
    public function isSelfService()
    {
        return $this->isCustomerDropoff() && $this->isCustomerPickup();
    }
}
