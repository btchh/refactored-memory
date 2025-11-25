<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\CalComService;
use App\Models\Booking;
use App\Http\Requests\User\CreateBooking;
use App\Http\Requests\User\UpdateBooking;

class BookingController extends Controller
{
    use \App\Traits\Responses;

    public function __construct(
        private CalComService $calComService
    ) {}

    /**
     * Show user bookings view
     * 
     * @return \Illuminate\View\View
     */
    public function viewBookings()
    {
        return view('user.bookings');
    }

    /**
     * Get user's bookings data via AJAX
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserBookingsData()
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return $this->errorResponse('User not authenticated', [], 401);
        }

        try {
            $result = $this->calComService->getUserBookings($user->id);

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
            Log::error('BookingController::getUserBookingsData - Error fetching bookings', [
                'user_id' => $user->id,
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
     * Create a new booking for authenticated user
     * 
     * @param CreateBooking $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBooking(CreateBooking $request)
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return $this->errorResponse('User not authenticated', [], 401);
        }

        try {
            $bookingData = $request->validated();
            
            $result = $this->calComService->createBooking($user->id, $bookingData);

            if ($result['success']) {
                Log::info('BookingController::createBooking - Booking created successfully', [
                    'user_id' => $user->id,
                    'booking_id' => $result['data']->id
                ]);

                return $this->successResponse(
                    $result['message'],
                    ['booking' => $result['data']],
                    201
                );
            }

            return $this->errorResponse(
                $result['message'],
                $result['errors'] ?? [],
                400
            );
        } catch (\Exception $e) {
            Log::error('BookingController::createBooking - Error creating booking', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse(
                'Failed to create booking',
                [],
                500
            );
        }
    }

    /**
     * Update user's own booking
     * 
     * @param UpdateBooking $request
     * @param int $bookingId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBooking(UpdateBooking $request, int $bookingId)
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return $this->errorResponse('User not authenticated', [], 401);
        }

        try {
            // Find the booking and verify ownership
            $booking = Booking::find($bookingId);

            if (!$booking) {
                return $this->errorResponse('Booking not found', [], 404);
            }

            // Authorization check: ensure user owns this booking
            if ($booking->user_id !== $user->id) {
                Log::warning('BookingController::updateBooking - Unauthorized access attempt', [
                    'user_id' => $user->id,
                    'booking_id' => $bookingId,
                    'booking_owner_id' => $booking->user_id
                ]);

                return $this->errorResponse(
                    'You are not authorized to update this booking',
                    [],
                    403
                );
            }

            $updateData = $request->validated();
            
            $result = $this->calComService->updateBooking(
                $booking->calcom_booking_id,
                $updateData
            );

            if ($result['success']) {
                Log::info('BookingController::updateBooking - Booking updated successfully', [
                    'user_id' => $user->id,
                    'booking_id' => $bookingId
                ]);

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
            Log::error('BookingController::updateBooking - Error updating booking', [
                'user_id' => $user->id,
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
     * Cancel user's own booking
     * 
     * @param int $bookingId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelBooking(int $bookingId, Request $request)
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return $this->errorResponse('User not authenticated', [], 401);
        }

        try {
            // Find the booking and verify ownership
            $booking = Booking::find($bookingId);

            if (!$booking) {
                return $this->errorResponse('Booking not found', [], 404);
            }

            // Authorization check: ensure user owns this booking
            if ($booking->user_id !== $user->id) {
                Log::warning('BookingController::cancelBooking - Unauthorized access attempt', [
                    'user_id' => $user->id,
                    'booking_id' => $bookingId,
                    'booking_owner_id' => $booking->user_id
                ]);

                return $this->errorResponse(
                    'You are not authorized to cancel this booking',
                    [],
                    403
                );
            }

            $reason = $request->input('reason', 'Cancelled by user');
            
            $result = $this->calComService->cancelBooking(
                $booking->calcom_booking_id,
                $reason
            );

            if ($result['success']) {
                Log::info('BookingController::cancelBooking - Booking cancelled successfully', [
                    'user_id' => $user->id,
                    'booking_id' => $bookingId,
                    'reason' => $reason
                ]);

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
            Log::error('BookingController::cancelBooking - Error cancelling booking', [
                'user_id' => $user->id,
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
}
