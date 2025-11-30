<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BookingManagementController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Transaction::with(['user', 'services', 'products'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                  ->orWhere('lname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(20);

        $stats = [
            'all' => Transaction::count(),
            'pending' => Transaction::where('status', 'pending')->count(),
            'in_progress' => Transaction::where('status', 'in_progress')->count(),
            'completed' => Transaction::where('status', 'completed')->count(),
            'cancelled' => Transaction::where('status', 'cancelled')->count(),
        ];

        return view('admin.bookings.manage', compact('bookings', 'stats', 'status', 'search'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $booking = Transaction::findOrFail($id);
        $booking->status = $validated['status'];
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'booking' => $booking
        ]);
    }

    public function show($id)
    {
        $booking = Transaction::with(['user', 'services', 'products'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'customer' => [
                    'name' => $booking->user->fname . ' ' . $booking->user->lname,
                    'email' => $booking->user->email,
                    'phone' => $booking->user->phone,
                ],
                'booking_date' => $booking->formatted_date,
                'booking_time' => $booking->formatted_time,
                'pickup_address' => $booking->pickup_address,
                'item_type' => ucfirst($booking->item_type),
                'weight' => $booking->weight ? $booking->weight . ' kg' : 'N/A',
                'status' => ucfirst($booking->status),
                'total_price' => '₱' . number_format($booking->total_price, 2),
                'services' => $booking->services->map(function($service) {
                    return [
                        'name' => $service->service_name,
                        'price' => '₱' . number_format($service->pivot->price_at_purchase, 2)
                    ];
                }),
                'products' => $booking->products->map(function($product) {
                    return [
                        'name' => $product->product_name,
                        'price' => '₱' . number_format($product->pivot->price_at_purchase, 2)
                    ];
                }),
                'notes' => $booking->notes ?? 'No notes',
                'created_at' => $booking->created_at->format('M d, Y g:i A'),
            ]
        ]);
    }

    public function updateWeight(Request $request, $id)
    {
        $validated = $request->validate([
            'weight' => 'required|numeric|min:0',
        ]);

        $booking = Transaction::findOrFail($id);
        $booking->weight = $validated['weight'];
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Weight updated successfully',
            'booking' => $booking
        ]);
    }
}
