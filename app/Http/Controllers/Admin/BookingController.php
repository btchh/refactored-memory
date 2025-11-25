<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\CalComService;
use App\Models\Booking;
use App\Http\Requests\Admin\ManageBooking;
use App\Http\Requests\Admin\SearchBookings;

class BookingController extends Controller
{
    use \App\Traits\Responses;

    public function __construct(
        private CalComService $calComService
    ) {}

    /**
     * Show admin bookings view
     * 
     * @return \Illuminate\View\View
     */
    public function viewAllBookings()
    {
        return view('admin.bookings.index');
    }

    /**
     * Get all bookings data with filtering via AJAX
     * 
     * @param SearchBookings $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBookingsData(SearchBookings $request)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return $this->errorResponse('Admin not authenticated', [], 401);
        }

        try {
            // Check if any filters are applied
            $filters = $request->only(['user_id', 'status', 'start_date', 'end_date', 'search']);
            $hasFilters = !empty(array_filter($filters));

            if ($hasFilters) {
                // Use search/filter method
                $result = $this->calComService->searchBookings($filters);
            } else {
                // Get all bookings without filters
                $result = $this->calComService->getAllBookings();
            }

            if ($result['success']) {
                return $this->successResponse(
                    $result['message'],
                    ['bookings' => $result['data']]
                );
            }

            return $this->errorResponse(
                $result['message'],
                $result['errors'] ?? [],
                400
            );
        } catch (\Exception $e) {
            Log::error('AdminBookingController::getAllBookingsData - Error fetching bookings', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse(
                'Failed to retrieve bookings',
                [],
                500
            );
        }
    }

    /**
     * Update any user's booking (admin management)
     * 
     * @param ManageBooking $request
     * @param int $bookingId
     * @return \Illuminate\Http\JsonResponse
     */
    public function manageBooking(ManageBooking $request, int $bookingId)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return $this->errorResponse('Admin not authenticated', [], 401);
        }

        try {
            // Find the booking
            $booking = Booking::find($bookingId);

            if (!$booking) {
                return $this->errorResponse('Booking not found', [], 404);
            }

            $updateData = $request->validated();
            
            $result = $this->calComService->updateBooking(
                $booking->calcom_booking_id,
                $updateData
            );

            if ($result['success']) {
                Log::info('AdminBookingController::manageBooking - Booking updated by admin', [
                    'admin_id' => $admin->id,
                    'booking_id' => $bookingId,
                    'user_id' => $booking->user_id
                ]);

                // TODO: Send notification to user about admin modification
                // This will be implemented in task 12

                return $this->successResponse(
                    $result['message'],
                    ['booking' => $result['data']]
                );
            }

            return $this->errorResponse(
                $result['message'],
                $result['errors'] ?? [],
                400
            );
        } catch (\Exception $e) {
            Log::error('AdminBookingController::manageBooking - Error updating booking', [
                'admin_id' => $admin->id,
                'booking_id' => $bookingId,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse(
                'Failed to update booking',
                [],
                500
            );
        }
    }

    /**
     * Cancel any user's booking (admin action)
     * 
     * @param int $bookingId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelUserBooking(int $bookingId, Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return $this->errorResponse('Admin not authenticated', [], 401);
        }

        try {
            // Find the booking
            $booking = Booking::find($bookingId);

            if (!$booking) {
                return $this->errorResponse('Booking not found', [], 404);
            }

            $reason = $request->input('reason', 'Cancelled by administrator');
            
            $result = $this->calComService->cancelBooking(
                $booking->calcom_booking_id,
                $reason
            );

            if ($result['success']) {
                Log::info('AdminBookingController::cancelUserBooking - Booking cancelled by admin', [
                    'admin_id' => $admin->id,
                    'booking_id' => $bookingId,
                    'user_id' => $booking->user_id,
                    'reason' => $reason
                ]);

                // TODO: Send notification to user about admin cancellation
                // This will be implemented in task 12

                return $this->successResponse(
                    $result['message'],
                    ['booking' => $result['data']]
                );
            }

            return $this->errorResponse(
                $result['message'],
                $result['errors'] ?? [],
                400
            );
        } catch (\Exception $e) {
            Log::error('AdminBookingController::cancelUserBooking - Error cancelling booking', [
                'admin_id' => $admin->id,
                'booking_id' => $bookingId,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse(
                'Failed to cancel booking',
                [],
                500
            );
        }
    }

    /**
     * Search bookings with filters
     * 
     * @param SearchBookings $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchBookings(SearchBookings $request)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            return $this->errorResponse('Admin not authenticated', [], 401);
        }

        try {
            $filters = $request->validated();
            
            $result = $this->calComService->searchBookings($filters);

            if ($result['success']) {
                Log::info('AdminBookingController::searchBookings - Search completed', [
                    'admin_id' => $admin->id,
                    'filters' => $filters,
                    'results_count' => count($result['data'])
                ]);

                return $this->successResponse(
                    $result['message'],
                    ['bookings' => $result['data']]
                );
            }

            return $this->errorResponse(
                $result['message'],
                $result['errors'] ?? [],
                400
            );
        } catch (\Exception $e) {
            Log::error('AdminBookingController::searchBookings - Error searching bookings', [
                'admin_id' => $admin->id,
                'filters' => $request->validated(),
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse(
                'Failed to search bookings',
                [],
                500
            );
        }
    }
}
