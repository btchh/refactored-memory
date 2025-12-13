<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\HasFilters;
use App\Models\Transaction;
use App\Services\FilterService;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    use HasFilters;

    public function __construct()
    {
        $this->initializeFilters();
    }
    /**
     * Show booking history with date filtering
     */
    public function history(Request $request)
    {
        // Base query for user's historical bookings
        $baseQuery = Transaction::where('user_id', auth()->id())
            ->where(function ($query) {
                $query->where('status', 'cancelled')
                      ->orWhere(function ($q) {
                          $q->where('status', 'completed')
                            ->where(function ($q2) {
                                $q2->whereNull('completed_at')
                                   ->orWhere('completed_at', '<', now()->subHour());
                            });
                      });
            })
            ->with(['services', 'products', 'admin'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc');

        // Apply filters using the centralized FilterService
        $filteredQuery = $this->applyFilters($baseQuery, $request, FilterService::userHistoryConfig());
        
        $transactions = $filteredQuery->get();

        // Format bookings for the view
        $bookings = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'date' => $transaction->formatted_date,
                'time' => $transaction->formatted_time,
                'total' => $transaction->total_price,
                'status' => $transaction->status,
                'item_type' => $transaction->item_type,
                'service_description' => $transaction->service_description,
                'pickup_address' => $transaction->pickup_address,
                'branch_name' => $transaction->admin?->branch_name,
                'branch_address' => $transaction->admin?->branch_address,
                'services' => $transaction->services->map(function ($service) {
                    return [
                        'name' => $service->service_name,
                        'price' => $service->pivot->price_at_purchase
                    ];
                }),
                'products' => $transaction->products->map(function ($product) {
                    return [
                        'name' => $product->product_name,
                        'price' => $product->pivot->price_at_purchase
                    ];
                }),
                'is_upcoming' => $transaction->isUpcoming(),
                'is_today' => $transaction->isToday(),
                'completed_at' => $transaction->completed_at?->format('M d, Y h:i A'),
            ];
        });

        // Get filter data for the view
        $filterData = $this->parseDateFilters($request);

        return view('user.history.index', [
            'bookings' => $bookings,
            'startDate' => $filterData['start_date'],
            'endDate' => $filterData['end_date']
        ]);
    }

    /**
     * Show booking status (active bookings only)
     */
    public function showStatus()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->active() // Use the active scope
            ->with(['services', 'products', 'admin'])
            ->latest()
            ->get();

        // Format bookings for the view
        $bookings = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'date' => $transaction->formatted_date,
                'time' => $transaction->formatted_time,
                'datetime' => $transaction->formatted_date_time,
                'total' => $transaction->total_price,
                'status' => $transaction->status,
                'item_type' => $transaction->item_type,
                'service_description' => $transaction->service_description,
                'pickup_address' => $transaction->pickup_address,
                'branch_name' => $transaction->admin?->branch_name,
                'branch_address' => $transaction->admin?->branch_address,
                'services' => $transaction->services->map(function ($service) {
                    return [
                        'name' => $service->service_name,
                        'price' => $service->pivot->price_at_purchase
                    ];
                }),
                'products' => $transaction->products->map(function ($product) {
                    return [
                        'name' => $product->product_name,
                        'price' => $product->pivot->price_at_purchase
                    ];
                }),
                'is_upcoming' => $transaction->isUpcoming(),
                'is_today' => $transaction->isToday(),
            ];
        });

        return view('user.status.index', compact('bookings'));
    }
}

