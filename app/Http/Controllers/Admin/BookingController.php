<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Product;
use App\Services\BookingService;
use App\Services\CalApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    protected $bookingService;
    protected $calApiService;

    public function __construct(BookingService $bookingService, CalApiService $calApiService)
    {
        $this->bookingService = $bookingService;
        $this->calApiService = $calApiService;
    }

    /**
     * Show the booking management interface
     */
    public function index()
    {
        $adminId = Auth::guard('admin')->id();
        
        $services = Service::forAdmin($adminId)->get()->groupBy('item_type');
        $products = Product::forAdmin($adminId)->get()->groupBy('item_type');
        
        // Get all users for dropdown
        $users = User::select('id', 'fname', 'lname', 'email', 'phone')
            ->orderBy('fname')
            ->orderBy('lname')
            ->get();

        return view('admin.bookings.index', compact('services', 'products', 'users'));
    }

    /**
     * Search users by name or email (AJAX)
     */
    public function searchUsers(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('fname', 'like', "%{$query}%")
            ->orWhere('lname', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->fname . ' ' . $user->lname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'latitude' => $user->latitude,
                    'longitude' => $user->longitude,
                ];
            });

        return response()->json($users);
    }

    /**
     * Get bookings for a specific user (AJAX)
     */
    public function getUserBookings($userId)
    {
        $bookings = $this->bookingService->getUserBookings($userId);

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
                    'user' => $transaction->user ? [
                        'id' => $transaction->user->id,
                        'name' => $transaction->user->fname . ' ' . $transaction->user->lname,
                        'email' => $transaction->user->email,
                        'phone' => $transaction->user->phone,
                    ] : [
                        'id' => null,
                        'name' => 'Archived User',
                        'email' => 'N/A',
                        'phone' => 'N/A',
                    ],
                ];
            }),
        ]);
    }

    /**
     * Get bookings for a specific date (AJAX)
     */
    public function getBookingsByDate(Request $request)
    {
        $date = $request->input('date');

        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        // Get all admin IDs for this branch
        $admin = Auth::guard('admin')->user();
        $branchAdminIds = \App\Models\Admin::where('branch_address', $admin->branch_address)->pluck('id')->toArray();

        $bookings = $this->bookingService->getBookingsByDate($date, $branchAdminIds);

        return response()->json([
            'success' => true,
            'date' => $date,
            'count' => $bookings->count(),
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
                    'user' => $transaction->user ? [
                        'id' => $transaction->user->id,
                        'name' => $transaction->user->fname . ' ' . $transaction->user->lname,
                        'email' => $transaction->user->email,
                        'phone' => $transaction->user->phone,
                        'address' => $transaction->user->address,
                        'latitude' => $transaction->user->latitude,
                        'longitude' => $transaction->user->longitude,
                    ] : [
                        'id' => null,
                        'name' => 'Archived User',
                        'email' => 'N/A',
                        'phone' => 'N/A',
                        'address' => 'N/A',
                        'latitude' => null,
                        'longitude' => null,
                    ],
                ];
            }),
        ]);
    }

    /**
     * Get booking counts for calendar (AJAX)
     */
    public function getBookingCounts(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        if (!$month || !$year) {
            return response()->json(['error' => 'Month and year are required'], 400);
        }

        // Get all admin IDs for this branch
        $admin = Auth::guard('admin')->user();
        $branchAdminIds = \App\Models\Admin::where('branch_address', $admin->branch_address)->pluck('id')->toArray();

        $counts = $this->bookingService->getBookingCountsByMonth($year, $month, $branchAdminIds);

        return response()->json([
            'success' => true,
            'counts' => $counts,
        ]);
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

        $slots = $this->calApiService->getAvailableSlots($date);

        return response()->json([
            'success' => true,
            'slots' => $slots,
        ]);
    }

    /**
     * Create a new booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_type' => 'required|in:online,walkin',
            'user_id' => 'required_if:booking_type,online|nullable|exists:users,id',
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

        $validated['admin_id'] = Auth::guard('admin')->id();

        $result = $this->bookingService->createBooking($validated);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    /**
     * Update an existing booking
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'booking_date' => 'sometimes|date|after_or_equal:today',
            'booking_time' => 'sometimes',
            'pickup_address' => 'sometimes|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'item_type' => 'sometimes|in:clothes,comforter,shoes',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'notes' => 'nullable|string',
        ]);

        $result = $this->bookingService->updateBooking($id, $validated);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    /**
     * Reschedule a booking
     */
    public function reschedule(Request $request, $id)
    {
        $validated = $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
        ]);

        $result = $this->bookingService->rescheduleBooking(
            $id,
            $validated['booking_date'],
            $validated['booking_time']
        );

        return response()->json($result);
    }

    /**
     * Cancel a booking
     */
    public function cancel(Request $request, $id)
    {
        $reason = $request->input('reason');

        $result = $this->bookingService->cancelBooking($id, $reason);

        return response()->json($result);
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $result = $this->bookingService->changeStatus($id, $validated['status']);

        return response()->json($result);
    }

    /**
     * Calculate total price (AJAX)
     */
    public function calculateTotal(Request $request)
    {
        $serviceIds = $request->input('services', []);
        $productIds = $request->input('products', []);

        $total = $this->bookingService->calculateTotal($serviceIds, $productIds);

        return response()->json([
            'success' => true,
            'total' => $total,
        ]);
    }
}
