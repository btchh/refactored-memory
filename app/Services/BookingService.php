<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingService
{
    protected $calApiService;
    protected $messageService;
    protected $auditService;

    public function __construct(CalApiService $calApiService, MessageService $messageService, AuditService $auditService)
    {
        $this->calApiService = $calApiService;
        $this->messageService = $messageService;
        $this->auditService = $auditService;
    }

    /**
     * Create a new booking (transaction with scheduling)
     */
    public function createBooking(array $data)
    {
        Log::info('BookingService::createBooking called', ['data' => $data]);
        
        DB::beginTransaction();

        try {
            Log::info('Creating transaction record');
            
            // Create transaction with booking fields
            $transaction = Transaction::create([
                'user_id' => $data['user_id'] ?? null, // Can be null for walk-in
                'admin_id' => $data['admin_id'] ?? null,
                'booking_date' => $data['booking_date'],
                'booking_time' => $data['booking_time'],
                'pickup_method' => $data['pickup_method'] ?? 'customer_dropoff', // Default to customer dropoff for walk-in
                'delivery_method' => $data['delivery_method'] ?? 'customer_pickup', // Default to customer pickup for walk-in
                'pickup_address' => $data['pickup_address'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'item_type' => $data['item_type'],
                'notes' => $data['notes'] ?? null,
                'weight' => $data['weight'] ?? null,
                'total_price' => 0, // Will be calculated
                'status' => 'pending',
                'booking_type' => $data['booking_type'] ?? 'online',
            ]);

            // Attach services (validate they belong to the correct admin)
            if (!empty($data['services'])) {
                foreach ($data['services'] as $serviceId) {
                    $service = Service::where('id', $serviceId)
                        ->where('admin_id', $data['admin_id'])
                        ->first();
                    if ($service) {
                        $transaction->services()->attach($serviceId, [
                            'price_at_purchase' => $service->price,
                        ]);
                    } else {
                        Log::warning('Service not found or does not belong to admin', [
                            'service_id' => $serviceId,
                            'admin_id' => $data['admin_id'],
                        ]);
                    }
                }
            }

            // Attach products (validate they belong to the correct admin)
            if (!empty($data['products'])) {
                foreach ($data['products'] as $productId) {
                    $product = Product::where('id', $productId)
                        ->where('admin_id', $data['admin_id'])
                        ->first();
                    if ($product) {
                        $transaction->products()->attach($productId, [
                            'price_at_purchase' => $product->price,
                        ]);
                    } else {
                        Log::warning('Product not found or does not belong to admin', [
                            'product_id' => $productId,
                            'admin_id' => $data['admin_id'],
                        ]);
                    }
                }
            }

            // Recalculate total price
            $transaction->refresh();
            $transaction->calculateTotalPrice();
            $transaction->save();

            Log::info('Transaction created successfully', ['transaction_id' => $transaction->id]);

            // Load relationships needed for CalAPI event
            $transaction->load('user', 'services', 'products');

            // Create CalAPI event
            Log::info('Creating CalAPI event');
            $eventId = $this->calApiService->createEvent($transaction);
            if ($eventId) {
                $transaction->update(['calapi_event_id' => $eventId]);
                Log::info('CalAPI event created', ['event_id' => $eventId]);
            } else {
                Log::warning('CalAPI event creation returned null');
            }

            // Clear cache for this date
            $this->calApiService->clearCache($data['booking_date']);

            DB::commit();
            Log::info('Transaction committed successfully');

            // Send booking confirmation SMS
            $this->sendBookingConfirmedSms($transaction->fresh()->load('user'));

            // Send reminder if booking is tomorrow
            $this->sendReminderIfTomorrow($transaction);

            // Notify admin of new booking
            $this->sendNewBookingNotification($transaction->fresh()->load('user', 'admin'));

            return [
                'success' => true,
                'booking' => $transaction->load('user', 'services', 'products'),
                'message' => 'Booking created successfully',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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

            // Update services if provided (validate they belong to the correct admin)
            if (isset($data['services'])) {
                $transaction->services()->detach();
                foreach ($data['services'] as $serviceId) {
                    $service = Service::where('id', $serviceId)
                        ->where('admin_id', $transaction->admin_id)
                        ->first();
                    if ($service) {
                        $transaction->services()->attach($serviceId, [
                            'price_at_purchase' => $service->price,
                        ]);
                    } else {
                        Log::warning('Service not found or does not belong to admin', [
                            'service_id' => $serviceId,
                            'admin_id' => $transaction->admin_id,
                        ]);
                    }
                }
            }

            // Update products if provided (validate they belong to the correct admin)
            if (isset($data['products'])) {
                $transaction->products()->detach();
                foreach ($data['products'] as $productId) {
                    $product = Product::where('id', $productId)
                        ->where('admin_id', $transaction->admin_id)
                        ->first();
                    if ($product) {
                        $transaction->products()->attach($productId, [
                            'price_at_purchase' => $product->price,
                        ]);
                    } else {
                        Log::warning('Product not found or does not belong to admin', [
                            'product_id' => $productId,
                            'admin_id' => $transaction->admin_id,
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
            $transaction = Transaction::with('user')->findOrFail($transactionId);
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

            // Send SMS notification
            $this->sendRescheduledSms($transaction->fresh()->load('user'));

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
    public function cancelBooking($transactionId, $reason = null, $byAdmin = false)
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::with('user')->findOrFail($transactionId);

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

            // Send SMS notification
            $this->sendCancelledSms($transaction, $reason, $byAdmin);

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
            $transaction = Transaction::with('user')->findOrFail($transactionId);
            $oldStatus = $transaction->status;
            
            // Set completed_at timestamp when status changes to completed
            $updateData = ['status' => $status];
            if ($status === 'completed' && $oldStatus !== 'completed') {
                $updateData['completed_at'] = now();
            }
            
            $transaction->update($updateData);

            // Send SMS for key status changes
            $this->sendStatusChangeSms($transaction, $status, $oldStatus);

            // Audit log
            if (Auth::guard('admin')->check()) {
                $this->auditService->logStatusChange(
                    Transaction::class,
                    $transaction,
                    $oldStatus,
                    $status,
                    "Booking #{$transaction->id} status changed from {$oldStatus} to {$status}"
                );
            }

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
     * Get bookings for a specific date (optionally filtered by branch)
     */
    public function getBookingsByDate($date, $branchAdminIds = null)
    {
        $query = Transaction::with(['services', 'products', 'admin', 'user'])
            ->whereDate('booking_date', $date);
        
        if ($branchAdminIds) {
            $query->whereIn('admin_id', $branchAdminIds);
        }
        
        return $query->orderBy('booking_time', 'asc')->get();
    }

    /**
     * Get booking counts by month for calendar badges (optionally filtered by branch)
     */
    public function getBookingCountsByMonth($year, $month, $branchAdminIds = null)
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        $query = Transaction::whereBetween('booking_date', [$startDate, $endDate]);
        
        if ($branchAdminIds) {
            $query->whereIn('admin_id', $branchAdminIds);
        }
        
        $bookings = $query->selectRaw('DATE(booking_date) as date, COUNT(*) as count')
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

    /**
     * Send SMS for status changes (only key statuses)
     */
    private function sendStatusChangeSms($transaction, $newStatus, $oldStatus)
    {
        if (!$transaction->user || !$transaction->user->phone) {
            return;
        }

        try {
            $data = $this->getSmsData($transaction);

            switch ($newStatus) {
                case 'completed':
                    $data['action'] = $transaction->isBranchDelivery() ? 'delivery' : 'pickup';
                    $this->messageService->sendLaundryCompleted($transaction->user->phone, $data);
                    break;

                case 'out_for_delivery':
                    $data['eta'] = 'within 1-2 hours';
                    $this->messageService->sendOutForDelivery($transaction->user->phone, $data);
                    break;

                case 'delivered':
                    $this->messageService->sendDelivered($transaction->user->phone, $data);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send status SMS', [
                'booking_id' => $transaction->id,
                'status' => $newStatus,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send SMS for cancelled booking
     */
    private function sendCancelledSms($transaction, $reason, $byAdmin)
    {
        try {
            $data = $this->getSmsData($transaction);
            $data['reason'] = $reason ?: 'No reason provided';

            // Notify customer
            if ($transaction->user && $transaction->user->phone) {
                if ($byAdmin) {
                    $this->messageService->sendBookingCancelledByAdmin($transaction->user->phone, $data);
                } else {
                    $this->messageService->sendBookingCancelled($transaction->user->phone, $data);
                }
            }

            // Notify admin when customer cancels
            if (!$byAdmin && $transaction->admin && $transaction->admin->phone) {
                $this->messageService->notifyAdminOfCancellation($transaction->admin->phone, $data);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation SMS', [
                'booking_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send SMS for rescheduled booking
     */
    private function sendRescheduledSms($transaction)
    {
        if (!$transaction->user || !$transaction->user->phone) {
            return;
        }

        try {
            $data = $this->getSmsData($transaction);
            $this->messageService->sendBookingRescheduled($transaction->user->phone, $data);
        } catch (\Exception $e) {
            Log::error('Failed to send reschedule SMS', [
                'booking_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send booking confirmation SMS
     */
    private function sendBookingConfirmedSms($transaction)
    {
        if (!$transaction->user || !$transaction->user->phone) {
            return;
        }

        try {
            $data = $this->getSmsData($transaction);
            $this->messageService->sendBookingConfirmed($transaction->user->phone, $data);
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation SMS', [
                'booking_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send reminder if booking is for tomorrow
     */
    private function sendReminderIfTomorrow($transaction)
    {
        if (!$transaction->user || !$transaction->user->phone) {
            return;
        }

        $bookingDate = \Carbon\Carbon::parse($transaction->booking_date);
        $tomorrow = now()->addDay()->startOfDay();

        // Only send reminder if booking is tomorrow
        if (!$bookingDate->isSameDay($tomorrow)) {
            return;
        }

        try {
            $data = $this->getSmsData($transaction);

            if ($transaction->isBranchDelivery()) {
                $this->messageService->sendDeliveryReminder($transaction->user->phone, $data);
            } else {
                $this->messageService->sendPickupReminder($transaction->user->phone, $data);
            }

            Log::info('Booking reminder sent', [
                'booking_id' => $transaction->id,
                'user_id' => $transaction->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking reminder', [
                'booking_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send new booking notification to admin
     */
    private function sendNewBookingNotification($transaction)
    {
        if (!$transaction->admin || !$transaction->admin->phone) {
            return;
        }

        try {
            $data = $this->getSmsData($transaction);
            $this->messageService->notifyAdminOfNewBooking($transaction->admin->phone, $data);
        } catch (\Exception $e) {
            Log::error('Failed to send new booking notification to admin', [
                'booking_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get common SMS data from transaction
     */
    private function getSmsData($transaction): array
    {
        return [
            'customer_name' => $transaction->user->fname ?? 'Walk-in Customer',
            'booking_id' => $transaction->id,
            'schedule' => date('M j, Y', strtotime($transaction->booking_date)) . ' at ' . date('g:i A', strtotime($transaction->booking_time)),
            'service_description' => $transaction->service_description,
        ];
    }
}
