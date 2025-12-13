<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\HasFilters;
use App\Models\Transaction;
use App\Services\BookingService;
use App\Services\CalApiService;
use App\Services\FilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    use HasFilters;

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
        $this->initializeFilters();
    }

    /**
     * Show the booking form
     */
    public function showBooking()
    {
        $user = Auth::guard('web')->user();
        
        // Get unique branches (group by branch_address to get one representative admin per branch)
        $branches = \App\Models\Admin::select('id', 'branch_name', 'branch_address', 'phone')
            ->whereNotNull('branch_address')
            ->where('branch_address', '!=', '')
            ->orderBy('branch_name')
            ->get()
            ->groupBy('branch_address')
            ->map(function ($adminsInBranch) {
                // Get the first admin as representative for this branch
                $representative = $adminsInBranch->first();
                return [
                    'id' => $representative->id, // Use first admin's ID for the branch
                    'branch_name' => $representative->branch_name,
                    'address' => $representative->branch_address,
                    'phone' => $representative->phone,
                ];
            })
            ->sortBy('branch_name')
            ->values(); // Reset array keys
        
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
            'services.*' => 'integer|exists:services,id',
            'products' => 'nullable|array',
            'products.*' => 'integer|exists:products,id',
            'notes' => 'nullable|string',
            'weight' => 'nullable|numeric',
        ]);

        // Additional validation: ensure services and products belong to the selected admin
        if (!empty($validated['services'])) {
            $validServices = \App\Models\Service::whereIn('id', $validated['services'])
                ->where('admin_id', $validated['admin_id'])
                ->pluck('id')
                ->toArray();
            
            if (count($validServices) !== count($validated['services'])) {
                return redirect()->back()
                    ->with('error', 'One or more selected services do not belong to the selected branch')
                    ->withInput();
            }
        }

        if (!empty($validated['products'])) {
            $validProducts = \App\Models\Product::whereIn('id', $validated['products'])
                ->where('admin_id', $validated['admin_id'])
                ->pluck('id')
                ->toArray();
            
            if (count($validProducts) !== count($validated['products'])) {
                return redirect()->back()
                    ->with('error', 'One or more selected products do not belong to the selected branch')
                    ->withInput();
            }
        }

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

    /**
     * Cancel a booking (AJAX)
     */
    public function cancelBooking(Request $request, $id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        // Only allow cancellation of pending bookings
        if (!in_array($transaction->status, ['pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending bookings can be cancelled'
            ], 400);
        }

        // Check if booking is in the future
        $bookingDateTime = \Carbon\Carbon::parse($transaction->booking_date->format('Y-m-d') . ' ' . $transaction->booking_time);
        if ($bookingDateTime->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel bookings that have already passed'
            ], 400);
        }

        $reason = $request->input('reason', 'Cancelled by customer');

        // Use booking service to handle cancellation with notifications
        $result = $this->bookingService->cancelBooking($id, $reason, false); // byAdmin = false

        return response()->json($result);
    }
}
