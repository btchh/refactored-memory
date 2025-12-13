<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\HasFilters;
use App\Models\Transaction;
use App\Services\AuditService;
use App\Services\FilterService;
use Illuminate\Http\Request;

class BookingManagementController extends Controller
{
    use HasFilters;

    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
        $this->initializeFilters();
    }
    public function index(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        
        // Get all admin IDs for this branch
        $branchAdminIds = \App\Models\Admin::where('branch_address', $admin->branch_address)->pluck('id');

        // Base query for this branch
        $baseQuery = Transaction::with(['user', 'services', 'products', 'admin'])
            ->whereIn('admin_id', $branchAdminIds)
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc');

        // Apply filters using the centralized FilterService
        $filteredQuery = $this->applyFilters(clone $baseQuery, $request, FilterService::adminManagementConfig());
        
        // Get paginated results
        $bookings = $this->getPaginatedResults($filteredQuery, $request, 20);

        // Get filter statistics
        $stats = $this->getFilterStats($baseQuery);

        // Build response data
        $responseData = $this->buildFilterResponse($request, $bookings, $stats);

        return view('admin.bookings.manage', [
            'bookings' => $responseData['results'],
            'stats' => $responseData['stats'],
            'status' => $request->get('status', 'all'),
            'search' => $request->get('search', ''),
            'startDate' => $responseData['filters']['start_date'],
            'endDate' => $responseData['filters']['end_date']
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $admin = auth()->guard('admin')->user();
        $branchAdminIds = \App\Models\Admin::where('branch_address', $admin->branch_address)->pluck('id');
        
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        // Only allow updating bookings for this branch
        $booking = Transaction::whereIn('admin_id', $branchAdminIds)->findOrFail($id);
        $oldStatus = $booking->status;
        $booking->status = $validated['status'];
        $booking->save();

        // Log the status change
        $customerName = $booking->user ? $booking->user->fname . ' ' . $booking->user->lname : 'Unknown';
        $this->auditService->logStatusChange(
            Transaction::class,
            $booking,
            $oldStatus,
            $validated['status'],
            "Changed booking #{$booking->id} status from {$oldStatus} to {$validated['status']} (Customer: {$customerName})"
        );

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'booking' => $booking
        ]);
    }

    public function show($id)
    {
        $admin = auth()->guard('admin')->user();
        $branchAdminIds = \App\Models\Admin::where('branch_address', $admin->branch_address)->pluck('id');
        
        // Only allow viewing bookings for this branch
        $booking = Transaction::with(['user', 'services', 'products', 'admin'])
            ->whereIn('admin_id', $branchAdminIds)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'customer' => [
                    'name' => $booking->user ? $booking->user->fname . ' ' . $booking->user->lname : 'Archived User',
                    'email' => $booking->user ? $booking->user->email : 'N/A',
                    'phone' => $booking->user ? $booking->user->phone : 'N/A',
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
        $oldWeight = $booking->weight;
        $booking->weight = $validated['weight'];
        $booking->save();

        // Log the weight update
        $customerName = $booking->user ? $booking->user->fname . ' ' . $booking->user->lname : 'Unknown';
        $this->auditService->logUpdate(
            Transaction::class,
            $booking,
            ['weight' => $oldWeight],
            "Updated booking #{$booking->id} weight from {$oldWeight}kg to {$validated['weight']}kg (Customer: {$customerName})"
        );

        return response()->json([
            'success' => true,
            'message' => 'Weight updated successfully',
            'booking' => $booking
        ]);
    }
}
