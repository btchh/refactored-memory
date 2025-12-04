<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\BookingService;
use App\Services\CalApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Show the booking form
     */
    public function showBooking()
    {
        $user = Auth::guard('web')->user();
        
        // Get all branches/admins for selection
        $branches = \App\Models\Admin::select('id', 'fname', 'lname', 'admin_name', 'branch_address', 'phone')
            ->get()
            ->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'name' => $admin->fname . ' ' . $admin->lname,
                    'branch_name' => $admin->admin_name,
                    'address' => $admin->branch_address,
                    'phone' => $admin->phone,
                ];
            });
        
        // Services and products will be loaded via AJAX when branch is selected
        $services = collect([]);
        $products = collect([]);
        
        return view('user.bookings.index', compact('services', 'products', 'user', 'branches')); 
    }

    /**
     * Get services and products for a specific branch (AJAX)
     */
    public function getBranchPricing(Request $request)
    {
        $adminId = $request->input('admin_id');
        
        if (!$adminId) {
            return response()->json(['error' => 'Branch is required'], 400);
        }

        $services = \App\Models\Service::forAdmin($adminId)->get()->groupBy('item_type');
        $products = \App\Models\Product::forAdmin($adminId)->get()->groupBy('item_type');

        return response()->json([
            'success' => true,
            'services' => $services,
            'products' => $products,
        ]);
    }

    /**
     * Submit booking
     */
    public function submitBooking(Request $request)
    {
        $validated = $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'pickup_method' => 'required|in:branch_pickup,customer_dropoff',
            'delivery_method' => 'required|in:branch_delivery,customer_pickup',
            'pickup_address' => 'required_if:pickup_method,branch_pickup|nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'item_type' => 'required|in:clothes,comforter,shoes',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'notes' => 'nullable|string',
            'weight' => 'nullable|numeric',
        ]);

        // Ensure at least one service or product is selected
        if (empty($validated['services']) && empty($validated['products'])) {
            return redirect()->back()
                ->with('error', 'Please select at least one service or product')
                ->withInput();
        }

        $validated['user_id'] = auth()->id();

        $bookingService = app(BookingService::class);
        $result = $bookingService->createBooking($validated);

        if ($result['success']) {
            return redirect()->route('user.booking')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    /**
     * Get available time slots for a date (AJAX)
     */
    public function getAvailableSlots(Request $request)
    {
        $date = $request->input('date');

        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        // Validate date format
        try {
            $dateObj = \Carbon\Carbon::parse($date);
            
            // Check if date is in the past
            if ($dateObj->isPast() && !$dateObj->isToday()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot book dates in the past',
                    'slots' => []
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid date format',
                'slots' => []
            ], 400);
        }

        $calApiService = app(CalAPIService::class);
        $slots = $calApiService->getAvailableSlots($date);

        return response()->json([
            'success' => true,
            'slots' => $slots,
        ]);
    }

    /**
     * Get user's bookings (AJAX)
     */
    public function getMyBookings()
    {
        $bookingService = app(BookingService::class);
        $bookings = $bookingService->getUserBookings(auth()->id());

        return response()->json([
            'success' => true,
            'bookings' => $bookings->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'date' => $transaction->formatted_date,
                    'time' => $transaction->formatted_time,
                    'datetime' => $transaction->formatted_date_time,
                    'address' => $transaction->pickup_address,
                    'item_type' => $transaction->item_type,
                    'status' => $transaction->status,
                    'total' => $transaction->total_price,
                    'services' => $transaction->services->pluck('service_name')->join(', '),
                    'products' => $transaction->products->pluck('product_name')->join(', '),
                    'notes' => $transaction->notes,
                    'is_upcoming' => $transaction->isUpcoming(),
                ];
            }),
        ]);
    }

    /**
     * Calculate total price (AJAX)
     */
    public function calculateTotal(Request $request)
    {
        $serviceIds = $request->input('services', []);
        $productIds = $request->input('products', []);

        $bookingService = app(BookingService::class);
        $total = $bookingService->calculateTotal($serviceIds, $productIds);

        return response()->json([
            'success' => true,
            'total' => $total,
        ]);
    }
}
