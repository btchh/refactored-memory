<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class HistoryController extends Controller
{
    /**
     * Show booking history
     */
    public function history()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->with(['services', 'products'])
            ->latest()
            ->get();

        // Format bookings for the view
        $bookings = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'date' => $transaction->formatted_date,
                'time' => $transaction->formatted_time,
                'total' => number_format($transaction->total_price, 2),
                'status' => $transaction->status,
                'item_type' => $transaction->item_type,
                'pickup_address' => $transaction->pickup_address,
                'services' => $transaction->services->map(function ($service) {
                    return [
                        'name' => $service->service_name,
                        'price' => number_format($service->pivot->price_at_purchase, 2)
                    ];
                }),
                'products' => $transaction->products->map(function ($product) {
                    return [
                        'name' => $product->product_name,
                        'price' => number_format($product->pivot->price_at_purchase, 2)
                    ];
                }),
                'is_upcoming' => $transaction->isUpcoming(),
                'is_today' => $transaction->isToday(),
            ];
        });

        return view('user.history', compact('bookings'));
    }

    /**
     * Show booking status
     */
    public function showStatus()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->with(['services', 'products'])
            ->latest()
            ->get();

        // Format bookings for the view
        $bookings = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'date' => $transaction->formatted_date,
                'time' => $transaction->formatted_time,
                'datetime' => $transaction->formatted_date_time,
                'total' => number_format($transaction->total_price, 2),
                'status' => $transaction->status,
                'item_type' => $transaction->item_type,
                'pickup_address' => $transaction->pickup_address,
                'services' => $transaction->services->map(function ($service) {
                    return [
                        'name' => $service->service_name,
                        'price' => number_format($service->pivot->price_at_purchase, 2)
                    ];
                }),
                'products' => $transaction->products->map(function ($product) {
                    return [
                        'name' => $product->product_name,
                        'price' => number_format($product->pivot->price_at_purchase, 2)
                    ];
                }),
                'is_upcoming' => $transaction->isUpcoming(),
                'is_today' => $transaction->isToday(),
            ];
        });

        return view('user.status', compact('bookings'));
    }
}

