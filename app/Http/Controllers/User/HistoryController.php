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
            ->with(['services', 'products', 'admin'])
            ->latest()
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
                'service_type' => $transaction->service_type,
                'pickup_address' => $transaction->pickup_address,
                'branch_name' => $transaction->admin?->admin_name,
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

        return view('user.history.index', compact('bookings'));
    }

    /**
     * Show booking status
     */
    public function showStatus()
    {
        $transactions = Transaction::where('user_id', auth()->id())
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
                'service_type' => $transaction->service_type,
                'pickup_address' => $transaction->pickup_address,
                'branch_name' => $transaction->admin?->admin_name,
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

