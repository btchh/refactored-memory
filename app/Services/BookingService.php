<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingService
{
    protected $calApiService;

    public function __construct(CalAPIService $calApiService)
    {
        $this->calApiService = $calApiService;
    }

    /**
     * Create a new booking (transaction with scheduling)
     */
    public function createBooking(array $data)
    {
        DB::beginTransaction();

        try {
            // Create transaction with booking fields
            $transaction = Transaction::create([
                'user_id' => $data['user_id'],
                'admin_id' => $data['admin_id'] ?? null,
                'booking_date' => $data['booking_date'],
                'booking_time' => $data['booking_time'],
                'pickup_address' => $data['pickup_address'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'item_type' => $data['item_type'],
                'notes' => $data['notes'] ?? null,
                'weight' => $data['weight'] ?? null,
                'total_price' => 0, // Will be calculated
                'status' => 'pending',
            ]);

            // Attach services
            if (!empty($data['services'])) {
                foreach ($data['services'] as $serviceId) {
                    $service = Service::find($serviceId);
                    if ($service) {
                        $transaction->services()->attach($serviceId, [
                            'price_at_purchase' => $service->price,
                        ]);
                    }
                }
            }

            // Attach products
            if (!empty($data['products'])) {
                foreach ($data['products'] as $productId) {
                    $product = Product::find($productId);
                    if ($product) {
                        $transaction->products()->attach($productId, [
                            'price_at_purchase' => $product->price,
                        ]);
                    }
                }
            }

            // Recalculate total price
            $transaction->refresh();
            $transaction->calculateTotalPrice();
            $transaction->save();

            // Create CalAPI event
            $eventId = $this->calApiService->createEvent($transaction);
            if ($eventId) {
                $transaction->update(['calapi_event_id' => $eventId]);
            }

            // Clear cache for this date
            $this->calApiService->clearCache($data['booking_date']);

            DB::commit();

            return [
                'success' => true,
                'booking' => $transaction->load('user', 'services', 'products'),
                'message' => 'Booking created successfully',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create booking: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update an existing booking
     */
    public function updateBooking($transactionId, array $data)
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::with(['services', 'products'])->findOrFail($transactionId);

            // Update services if provided
            if (isset($data['services'])) {
                $transaction->services()->detach();
                foreach ($data['services'] as $serviceId) {
                    $service = Service::find($serviceId);
                    if ($service) {
                        $transaction->services()->attach($serviceId, [
                            'price_at_purchase' => $service->price,
                        ]);
                    }
                }
            }

            // Update products if provided
            if (isset($data['products'])) {
                $transaction->products()->detach();
                foreach ($data['products'] as $productId) {
                    $product = Product::find($productId);
                    if ($product) {
                        $transaction->products()->attach($productId, [
                            'price_at_purchase' => $product->price,
                        ]);
                    }
                }
            }

            // Recalculate total
            $transaction->refresh();
            $transaction->calculateTotalPrice();
            $transaction->save();

            // Update booking fields
            $transaction->update(array_filter([
                'booking_date' => $data['booking_date'] ?? $transaction->booking_date,
                'booking_time' => $data['booking_time'] ?? $transaction->booking_time,
                'pickup_address' => $data['pickup_address'] ?? $transaction->pickup_address,
                'latitude' => $data['latitude'] ?? $transaction->latitude,
                'longitude' => $data['longitude'] ?? $transaction->longitude,
                'item_type' => $data['item_type'] ?? $transaction->item_type,
                'notes' => $data['notes'] ?? $transaction->notes,
            ]));

            // Update CalAPI event
            if ($transaction->calapi_event_id) {
                $this->calApiService->updateEvent($transaction->calapi_event_id, $transaction);
            }

            // Clear cache
            $this->calApiService->clearCache($transaction->booking_date);

            DB::commit();

            return [
                'success' => true,
                'booking' => $transaction->fresh()->load('user', 'services', 'products'),
                'message' => 'Booking updated successfully',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking update failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update booking: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reschedule a booking to a new date/time
     */
    public function rescheduleBooking($transactionId, $newDate, $newTime)
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($transactionId);
            $oldDate = $transaction->booking_date;

            $transaction->update([
                'booking_date' => $newDate,
                'booking_time' => $newTime,
            ]);

            // Update CalAPI event
            if ($transaction->calapi_event_id) {
                $this->calApiService->updateEvent($transaction->calapi_event_id, $transaction);
            }

            // Clear cache for both old and new dates
            $this->calApiService->clearCache($oldDate);
            $this->calApiService->clearCache($newDate);

            DB::commit();

            return [
                'success' => true,
                'booking' => $transaction->fresh(),
                'message' => 'Booking rescheduled successfully',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking reschedule failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to reschedule booking: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking($transactionId, $reason = null)
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($transactionId);

            // Update status
            $transaction->update(['status' => 'cancelled']);

            // Delete CalAPI event
            if ($transaction->calapi_event_id) {
                $this->calApiService->deleteEvent($transaction->calapi_event_id);
            }

            // Add cancellation reason to notes
            if ($reason) {
                $transaction->update([
                    'notes' => ($transaction->notes ? $transaction->notes . "\n\n" : '') . "Cancelled: " . $reason,
                ]);
            }

            // Clear cache
            $this->calApiService->clearCache($transaction->booking_date);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Booking cancelled successfully',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking cancellation failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to cancel booking: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Change booking status
     */
    public function changeStatus($transactionId, $status)
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);
            $transaction->update(['status' => $status]);

            return [
                'success' => true,
                'message' => 'Status updated successfully',
            ];
        } catch (\Exception $e) {
            Log::error('Status change failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get bookings for a specific user
     */
    public function getUserBookings($userId)
    {
        return Transaction::with(['services', 'products', 'admin', 'user'])
            ->where('user_id', $userId)
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->get();
    }

    /**
     * Get bookings for a specific date
     */
    public function getBookingsByDate($date)
    {
        return Transaction::with(['services', 'products', 'admin', 'user'])
            ->whereDate('booking_date', $date)
            ->orderBy('booking_time', 'asc')
            ->get();
    }

    /**
     * Get booking counts by month for calendar badges
     */
    public function getBookingCountsByMonth($year, $month)
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        $bookings = Transaction::whereBetween('booking_date', [$startDate, $endDate])
            ->selectRaw('DATE(booking_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        return $bookings;
    }

    /**
     * Calculate total price for services and products
     */
    public function calculateTotal(array $serviceIds = [], array $productIds = [])
    {
        $total = 0;

        if (!empty($serviceIds)) {
            $total += Service::whereIn('id', $serviceIds)->sum('price');
        }

        if (!empty($productIds)) {
            $total += Product::whereIn('id', $productIds)->sum('price');
        }

        return $total;
    }
}
