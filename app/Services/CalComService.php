<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

class CalComService
{
    private string $apiKey;
    private string $baseUrl;
    private int $timeout = 30;
    private int $maxRetries = 3;
    private int $retryDelay = 1000; // milliseconds

    public function __construct()
    {
        $apiKey = config('services.calcom.api_key');
        
        if (empty($apiKey)) {
            throw new \Exception('Cal.com API key is not configured. Please set CALCOM_API_KEY in your .env file.');
        }
        
        $this->apiKey = $apiKey;
        $this->baseUrl = config('services.calcom.base_url');
    }

    /**
     * Create a new booking
     *
     * @param int $userId
     * @param array $bookingData
     * @return array
     */
    public function createBooking(int $userId, array $bookingData): array
    {
        try {
            Log::info('CalComService::createBooking - Creating booking', [
                'user_id' => $userId,
                'booking_data' => $bookingData
            ]);

            $payload = [
                'eventTypeId' => $bookingData['event_type_id'] ?? null,
                'start' => $bookingData['start_time'],
                'end' => $bookingData['end_time'],
                'responses' => [
                    'name' => $bookingData['attendee_name'] ?? '',
                    'email' => $bookingData['attendee_email'] ?? '',
                    'phone' => $bookingData['attendee_phone'] ?? '',
                    'notes' => $bookingData['notes'] ?? '',
                ],
                'timeZone' => $bookingData['timezone'] ?? 'UTC',
                'language' => $bookingData['language'] ?? 'en',
                'metadata' => $bookingData['metadata'] ?? [],
            ];

            $response = $this->makeApiRequest('POST', '/bookings', $payload);

            if ($response['success']) {
                $transformedData = $this->transformCalComBooking($response['data']);
                $transformedData['user_id'] = $userId;

                // Save to local database
                $booking = Booking::create($transformedData);

                // Invalidate cache
                $this->invalidateUserBookingsCache($userId);

                return [
                    'success' => true,
                    'data' => $booking,
                    'message' => 'Booking created successfully'
                ];
            }

            return $response;
        } catch (\Exception $e) {
            return $this->handleApiError($e, 'createBooking', ['user_id' => $userId]);
        }
    }

    /**
     * Get all bookings for a specific user
     *
     * @param int $userId
     * @return array
     */
    public function getUserBookings(int $userId): array
    {
        try {
            $cacheKey = "user_bookings_{$userId}";

            return Cache::remember($cacheKey, 300, function () use ($userId) {
                Log::info('CalComService::getUserBookings - Fetching bookings', [
                    'user_id' => $userId
                ]);

                // Fetch from local database
                $bookings = Booking::where('user_id', $userId)
                    ->orderBy('start_time', 'desc')
                    ->get();

                return [
                    'success' => true,
                    'data' => $bookings,
                    'message' => 'User bookings retrieved successfully'
                ];
            });
        } catch (\Exception $e) {
            return $this->handleApiError($e, 'getUserBookings', ['user_id' => $userId]);
        }
    }

    /**
     * Get all bookings (admin only)
     *
     * @return array
     */
    public function getAllBookings(): array
    {
        try {
            $cacheKey = "all_bookings";

            return Cache::remember($cacheKey, 300, function () {
                Log::info('CalComService::getAllBookings - Fetching all bookings');

                // Fetch from local database with user relationships
                $bookings = Booking::with('user')
                    ->orderBy('start_time', 'desc')
                    ->get();

                return [
                    'success' => true,
                    'data' => $bookings,
                    'message' => 'All bookings retrieved successfully'
                ];
            });
        } catch (\Exception $e) {
            return $this->handleApiError($e, 'getAllBookings', []);
        }
    }

    /**
     * Update an existing booking
     *
     * @param string $bookingId
     * @param array $data
     * @return array
     */
    public function updateBooking(string $bookingId, array $data): array
    {
        try {
            Log::info('CalComService::updateBooking - Updating booking', [
                'booking_id' => $bookingId,
                'data' => $data
            ]);

            // Find local booking
            $booking = Booking::where('calcom_booking_id', $bookingId)->first();

            if (!$booking) {
                return [
                    'success' => false,
                    'message' => 'Booking not found',
                    'code' => 'BOOKING_NOT_FOUND'
                ];
            }

            $payload = [];

            if (isset($data['start_time'])) {
                $payload['start'] = $data['start_time'];
            }
            if (isset($data['end_time'])) {
                $payload['end'] = $data['end_time'];
            }
            if (isset($data['attendee_name']) || isset($data['attendee_email']) || isset($data['notes'])) {
                $payload['responses'] = [
                    'name' => $data['attendee_name'] ?? $booking->attendee_name,
                    'email' => $data['attendee_email'] ?? $booking->attendee_email,
                    'notes' => $data['notes'] ?? $booking->notes,
                ];
            }

            // Make API call to Cal.com
            $response = $this->makeApiRequest('PATCH', "/bookings/{$bookingId}", $payload);

            if ($response['success']) {
                // Update local database
                $updateData = [];
                if (isset($data['title'])) $updateData['title'] = $data['title'];
                if (isset($data['description'])) $updateData['description'] = $data['description'];
                if (isset($data['start_time'])) $updateData['start_time'] = $data['start_time'];
                if (isset($data['end_time'])) $updateData['end_time'] = $data['end_time'];
                if (isset($data['status'])) $updateData['status'] = $data['status'];
                if (isset($data['attendee_email'])) $updateData['attendee_email'] = $data['attendee_email'];
                if (isset($data['attendee_name'])) $updateData['attendee_name'] = $data['attendee_name'];
                if (isset($data['attendee_phone'])) $updateData['attendee_phone'] = $data['attendee_phone'];
                if (isset($data['location'])) $updateData['location'] = $data['location'];
                if (isset($data['notes'])) $updateData['notes'] = $data['notes'];

                $booking->update($updateData);

                // Invalidate cache
                $this->invalidateUserBookingsCache($booking->user_id);
                Cache::forget('all_bookings');

                return [
                    'success' => true,
                    'data' => $booking->fresh(),
                    'message' => 'Booking updated successfully'
                ];
            }

            return $response;
        } catch (\Exception $e) {
            return $this->handleApiError($e, 'updateBooking', ['booking_id' => $bookingId]);
        }
    }

    /**
     * Cancel a booking
     *
     * @param string $bookingId
     * @param string $reason
     * @return array
     */
    public function cancelBooking(string $bookingId, string $reason = ''): array
    {
        try {
            Log::info('CalComService::cancelBooking - Cancelling booking', [
                'booking_id' => $bookingId,
                'reason' => $reason
            ]);

            // Find local booking
            $booking = Booking::where('calcom_booking_id', $bookingId)->first();

            if (!$booking) {
                return [
                    'success' => false,
                    'message' => 'Booking not found',
                    'code' => 'BOOKING_NOT_FOUND'
                ];
            }

            $payload = [
                'reason' => $reason,
            ];

            // Make API call to Cal.com
            $response = $this->makeApiRequest('DELETE', "/bookings/{$bookingId}", $payload);

            if ($response['success']) {
                // Update local database
                $booking->update([
                    'status' => 'cancelled',
                    'notes' => $booking->notes . "\nCancellation reason: " . $reason
                ]);

                // Invalidate cache
                $this->invalidateUserBookingsCache($booking->user_id);
                Cache::forget('all_bookings');

                return [
                    'success' => true,
                    'data' => $booking->fresh(),
                    'message' => 'Booking cancelled successfully'
                ];
            }

            return $response;
        } catch (\Exception $e) {
            return $this->handleApiError($e, 'cancelBooking', ['booking_id' => $bookingId]);
        }
    }

    /**
     * Search bookings with filters
     *
     * @param array $filters
     * @return array
     */
    public function searchBookings(array $filters): array
    {
        try {
            Log::info('CalComService::searchBookings - Searching bookings', [
                'filters' => $filters
            ]);

            $query = Booking::with('user');

            // Apply user filter
            if (isset($filters['user_id']) && !empty($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }

            // Apply date range filter
            if (isset($filters['start_date']) && !empty($filters['start_date'])) {
                $query->where('start_time', '>=', $filters['start_date']);
            }
            if (isset($filters['end_date']) && !empty($filters['end_date'])) {
                $query->where('start_time', '<=', $filters['end_date']);
            }

            // Apply status filter
            if (isset($filters['status']) && !empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            // Apply search term (searches in title, description, attendee name/email)
            if (isset($filters['search']) && !empty($filters['search'])) {
                $searchTerm = $filters['search'];
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhere('attendee_name', 'like', "%{$searchTerm}%")
                      ->orWhere('attendee_email', 'like', "%{$searchTerm}%");
                });
            }

            $bookings = $query->orderBy('start_time', 'desc')->get();

            return [
                'success' => true,
                'data' => $bookings,
                'message' => 'Bookings filtered successfully'
            ];
        } catch (\Exception $e) {
            return $this->handleApiError($e, 'searchBookings', ['filters' => $filters]);
        }
    }

    /**
     * Transform Cal.com API response to application format
     *
     * @param array $calcomData
     * @return array
     */
    private function transformCalComBooking(array $calcomData): array
    {
        return [
            'calcom_booking_id' => $calcomData['id'] ?? $calcomData['uid'] ?? uniqid('booking_'),
            'title' => $calcomData['title'] ?? $calcomData['eventType']['title'] ?? 'Booking',
            'description' => $calcomData['description'] ?? $calcomData['eventType']['description'] ?? null,
            'start_time' => $calcomData['startTime'] ?? $calcomData['start'] ?? now(),
            'end_time' => $calcomData['endTime'] ?? $calcomData['end'] ?? now()->addHour(),
            'status' => $this->mapCalComStatus($calcomData['status'] ?? 'pending'),
            'attendee_email' => $calcomData['attendees'][0]['email'] ?? $calcomData['responses']['email'] ?? null,
            'attendee_name' => $calcomData['attendees'][0]['name'] ?? $calcomData['responses']['name'] ?? null,
            'attendee_phone' => $calcomData['attendees'][0]['phone'] ?? $calcomData['responses']['phone'] ?? null,
            'location' => $calcomData['location'] ?? null,
            'notes' => $calcomData['responses']['notes'] ?? $calcomData['notes'] ?? null,
            'metadata' => $calcomData,
        ];
    }

    /**
     * Map Cal.com status to application status
     *
     * @param string $calcomStatus
     * @return string
     */
    private function mapCalComStatus(string $calcomStatus): string
    {
        $statusMap = [
            'ACCEPTED' => 'confirmed',
            'PENDING' => 'pending',
            'CANCELLED' => 'cancelled',
            'REJECTED' => 'cancelled',
            'confirmed' => 'confirmed',
            'pending' => 'pending',
            'cancelled' => 'cancelled',
        ];

        return $statusMap[$calcomStatus] ?? 'pending';
    }

    /**
     * Handle API errors with logging and standardized response
     *
     * @param \Exception $exception
     * @param string $method
     * @param array $context
     * @return array
     */
    private function handleApiError(\Exception $exception, string $method, array $context): array
    {
        Log::error("CalComService::{$method} - Error occurred", [
            'context' => $context,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'exception_type' => get_class($exception)
        ]);

        $message = 'An error occurred while processing your booking request';
        $code = 'BOOKING_ERROR';

        if ($exception instanceof ConnectionException) {
            $message = 'Failed to connect to Cal.com API. Please try again later.';
            $code = 'CONNECTION_ERROR';
        } elseif ($exception instanceof RequestException) {
            $response = $exception->response;
            if ($response) {
                $statusCode = $response->status();
                $responseData = $response->json();

                if ($statusCode === 401) {
                    $message = 'Authentication failed. Please check API credentials.';
                    $code = 'AUTH_ERROR';
                } elseif ($statusCode === 404) {
                    $message = 'Booking not found.';
                    $code = 'NOT_FOUND';
                } elseif ($statusCode === 422) {
                    $message = $responseData['message'] ?? 'Validation error occurred.';
                    $code = 'VALIDATION_ERROR';
                } elseif ($statusCode === 429) {
                    $message = 'Rate limit exceeded. Please try again later.';
                    $code = 'RATE_LIMIT';
                } elseif ($statusCode >= 500) {
                    $message = 'Cal.com service is temporarily unavailable. Please try again later.';
                    $code = 'SERVICE_ERROR';
                }
            }
        }

        return [
            'success' => false,
            'message' => $message,
            'code' => $code,
            'errors' => []
        ];
    }

    /**
     * Make API request with retry logic
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    private function makeApiRequest(string $method, string $endpoint, array $data = []): array
    {
        $attempt = 0;

        while ($attempt < $this->maxRetries) {
            try {
                $attempt++;

                Log::info('CalComService::makeApiRequest - Making request', [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'attempt' => $attempt
                ]);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->timeout($this->timeout)
                ->withOptions(['verify' => false])
                ->{strtolower($method)}($this->baseUrl . $endpoint, $data);

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'data' => $response->json(),
                        'message' => 'Request successful'
                    ];
                }

                // Handle rate limiting with retry
                if ($response->status() === 429 && $attempt < $this->maxRetries) {
                    $delay = $this->retryDelay * pow(2, $attempt - 1); // Exponential backoff
                    Log::warning('CalComService::makeApiRequest - Rate limited, retrying', [
                        'attempt' => $attempt,
                        'delay_ms' => $delay
                    ]);
                    usleep($delay * 1000); // Convert to microseconds
                    continue;
                }

                // For other errors, throw exception to be handled by handleApiError
                $response->throw();

            } catch (RequestException $e) {
                if ($e->response && $e->response->status() === 429 && $attempt < $this->maxRetries) {
                    $delay = $this->retryDelay * pow(2, $attempt - 1);
                    Log::warning('CalComService::makeApiRequest - Rate limited (exception), retrying', [
                        'attempt' => $attempt,
                        'delay_ms' => $delay
                    ]);
                    usleep($delay * 1000);
                    continue;
                }
                throw $e;
            }
        }

        // If all retries exhausted
        return [
            'success' => false,
            'message' => 'Maximum retry attempts exceeded',
            'code' => 'MAX_RETRIES_EXCEEDED'
        ];
    }

    /**
     * Invalidate user bookings cache
     *
     * @param int $userId
     * @return void
     */
    private function invalidateUserBookingsCache(int $userId): void
    {
        Cache::forget("user_bookings_{$userId}");
        Cache::forget('all_bookings');
    }
}
