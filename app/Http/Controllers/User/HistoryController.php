<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Show booking history with date filtering
     */
    public function history(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->where(function ($query) {
                $query->where('status', 'cancelled')
                      ->orWhere(function ($q) {
                          $q->where('status', 'completed')
                            ->where(function ($q2) {
                                $q2->whereNull('completed_at')
                                   ->orWhere('completed_at', '<', now()->subHour());
                            });
                      });
            });

        // Handle period-based filters
        $period = $request->input('period');
        $startDate = null;
        $endDate = null;

        if ($period) {
            switch ($period) {
                case 'today':
                    $query->whereDate('booking_date', today());
                    break;
                case 'week':
                    $query->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('booking_date', now()->month)
                          ->whereYear('booking_date', now()->year);
                    break;
                case 'year':
                    $query->whereYear('booking_date', now()->year);
                    break;
            }
        }

        // Apply custom date range if provided
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('booking_date', '>=', $request->start_date);
            $startDate = $request->start_date;
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('booking_date', '<=', $request->end_date);
            $endDate = $request->end_date;
        }

        $transactions = $query->with(['services', 'products', 'admin'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->get();

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

        return view('user.history.index', compact('bookings', 'startDate', 'endDate'));
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

