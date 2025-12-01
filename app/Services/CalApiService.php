<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CalApiService
{
    protected $apiKey;
    protected $baseUrl;
    protected $timezone;
    protected $apiVersion;

    protected $accountId;
    protected $calendarId;

    public function __construct()
    {
        $this->apiKey = config('calapi.api_key');
        $this->baseUrl = config('calapi.base_url', 'https://api.calapi.io');
        $this->accountId = config('calapi.account_id');
        $this->calendarId = config('calapi.calendar_id', 'primary');
        $this->timezone = config('calapi.timezone', 'Asia/Manila');
        $this->apiVersion = config('calapi.api_version', '2024-08-13');
    }

    /**
     * Check if CalAPI is properly configured
     */
    public function isConfigured()
    {
        return !empty($this->apiKey) && !empty($this->baseUrl);
    }

    /**
     * Make an API request with proper headers (CalAPI.io style)
     */
    protected function makeRequest($method, $endpoint, $data = [])
    {
        $url = $this->baseUrl . $endpoint;
        
        $options = [
            'verify' => config('app.env') === 'production',
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ];

        Log::info('CalAPI Request', [
            'method' => $method,
            'url' => $url,
            'data' => $data,
        ]);

        try {
            $request = Http::timeout(15)->withOptions($options)->withHeaders($headers);

            switch (strtoupper($method)) {
                case 'GET':
                    $response = $request->get($url, $data);
                    break;
                case 'POST':
                    $response = $request->post($url, $data);
                    break;
                case 'PUT':
                case 'PATCH':
                    $response = $request->patch($url, $data);
                    break;
                case 'DELETE':
                    $response = $request->delete($url);
                    break;
                default:
                    throw new \Exception("Unsupported HTTP method: $method");
            }

            Log::info('CalAPI Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('CalAPI Request Exception', [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Test API connection by listing calendars
     */
    public function testConnection()
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'CalAPI not configured',
            ];
        }

        try {
            // First, list calendars to verify connection
            $response = $this->makeRequest('GET', '/calendars');

            if ($response->successful()) {
                $calendars = $response->json('data', []);
                
                return [
                    'success' => true,
                    'message' => 'Connected to CalAPI.io - Google Calendar linked successfully!',
                    'calendars' => $calendars,
                    'calendar_count' => count($calendars),
                ];
            }

            return [
                'success' => false,
                'message' => 'API returned: ' . $response->status(),
                'body' => $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get list of available calendars
     */
    public function getCalendars()
    {
        try {
            $response = $this->makeRequest('GET', '/calendars');

            if ($response->successful()) {
                return $response->json('data', []);
            }

            Log::error('Failed to get calendars', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Exception getting calendars', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get the active calendar ID to use for events
     */
    protected function getActiveCalendarId()
    {
        // Check cache first
        $cacheKey = 'calapi_active_calendar_id';
        $cachedId = Cache::get($cacheKey);
        
        if ($cachedId) {
            return $cachedId;
        }

        // Get calendars from API
        $calendars = $this->getCalendars();
        
        if (empty($calendars)) {
            return null;
        }

        $calendarId = $this->calendarId;
        
        // If using 'primary', find a calendar with write access
        if ($calendarId === 'primary') {
            // First, look for a calendar with writer/owner access
            foreach ($calendars as $calendar) {
                if (isset($calendar['access_role']) && in_array($calendar['access_role'], ['writer', 'owner'])) {
                    $calendarId = $calendar['id'];
                    Log::info('Selected writable calendar', ['id' => $calendarId, 'title' => $calendar['title'] ?? 'Unknown']);
                    break;
                }
            }
            
            // Fallback: find primary calendar
            if ($calendarId === 'primary') {
                foreach ($calendars as $calendar) {
                    if (isset($calendar['primary']) && $calendar['primary']) {
                        $calendarId = $calendar['id'];
                        break;
                    }
                }
            }
            
            // If still no calendar found, use first writable or first available
            if ($calendarId === 'primary' && !empty($calendars)) {
                $calendarId = $calendars[0]['id'];
            }
        }

        // Cache for 1 hour
        Cache::put($cacheKey, $calendarId, now()->addHour());
        
        return $calendarId;
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableSlots($date, $duration = 60)
    {
        $cacheKey = "calapi_slots_{$date}_{$duration}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($date, $duration) {
            // Cal.com doesn't have a direct availability endpoint
            // We use default slots and filter based on existing bookings
            Log::info('Using default time slots', ['date' => $date]);
            return $this->getDefaultTimeSlots();
        });
    }

    /**
     * Create a calendar event for a transaction
     * Based on CalAPI.io documentation: https://docs.calapi.io/events#create-an-event
     */
    public function createEvent($transaction)
    {
        try {
            // Check if API key is configured
            if (empty($this->apiKey)) {
                Log::warning('CalAPI key not configured, skipping event creation', [
                    'transaction_id' => $transaction->id,
                ]);
                return null;
            }

            $startDateTime = Carbon::parse($transaction->booking_date->format('Y-m-d') . ' ' . $transaction->booking_time);
            $endDateTime = $startDateTime->copy()->addHour();

            // CalAPI.io event payload
            $serviceType = ucfirst($transaction->service_type ?? 'pickup');
            $payload = [
                'title' => "[{$serviceType}] {$transaction->user->username} - {$transaction->user->fname} {$transaction->user->lname}",
                'description' => $this->buildReceiptDescription($transaction),
                'start' => [
                    'date_time' => $startDateTime->toIso8601String(),
                    'time_zone' => $this->timezone,
                ],
                'end' => [
                    'date_time' => $endDateTime->toIso8601String(),
                    'time_zone' => $this->timezone,
                ],
                'location' => $transaction->pickup_address,
            ];

            // Get calendar ID (fetch once and cache if needed)
            $calendarId = $this->getActiveCalendarId();
            
            if (!$calendarId) {
                Log::error('No calendar ID available', [
                    'transaction_id' => $transaction->id,
                ]);
                return null;
            }

            Log::info('CalAPI createEvent request', [
                'transaction_id' => $transaction->id,
                'calendar_id' => $calendarId,
                'payload' => $payload,
            ]);

            $endpoint = "/calendars/{$calendarId}/events";
            $response = $this->makeRequest('POST', $endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                // CalAPI returns data nested in 'data' key
                $eventData = $data['data'] ?? $data;
                $eventId = $eventData['id'] ?? null;
                
                Log::info('CalAPI event created successfully', [
                    'transaction_id' => $transaction->id,
                    'event_id' => $eventId,
                    'response' => $data,
                ]);
                
                return $eventId;
            }

            Log::error('CalAPI createEvent failed', [
                'transaction_id' => $transaction->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('CalAPI createEvent exception', [
                'transaction_id' => $transaction->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Build receipt-style description for calendar event
     */
    protected function buildReceiptDescription($transaction)
    {
        $serviceType = ucfirst($transaction->service_type ?? 'pickup');
        $date = $transaction->booking_date->format('F d, Y');
        $time = Carbon::parse($transaction->booking_time)->format('g:i A');
        
        $receipt = "═══════════════════════════════════\n";
        $receipt .= "         WASHHOUR BOOKING RECEIPT\n";
        $receipt .= "═══════════════════════════════════\n\n";
        
        $receipt .= "📋 BOOKING DETAILS\n";
        $receipt .= "───────────────────────────────────\n";
        $receipt .= "Booking #: {$transaction->id}\n";
        $receipt .= "Date: {$date}\n";
        $receipt .= "Time: {$time}\n";
        $receipt .= "Service Type: {$serviceType}\n";
        $receipt .= "Item Type: " . ucfirst($transaction->item_type) . "\n\n";
        
        $receipt .= "👤 CUSTOMER INFO\n";
        $receipt .= "───────────────────────────────────\n";
        $receipt .= "Name: {$transaction->user->fname} {$transaction->user->lname}\n";
        $receipt .= "Username: {$transaction->user->username}\n";
        $receipt .= "Phone: {$transaction->user->phone}\n";
        $receipt .= "Email: {$transaction->user->email}\n";
        
        if ($transaction->pickup_address) {
            $receipt .= "Address: {$transaction->pickup_address}\n";
        }
        $receipt .= "\n";
        
        // Services section
        if ($transaction->services->count() > 0) {
            $receipt .= "🧺 SERVICES\n";
            $receipt .= "───────────────────────────────────\n";
            $servicesTotal = 0;
            foreach ($transaction->services as $service) {
                $price = $service->pivot->price_at_purchase ?? $service->price;
                $servicesTotal += $price;
                $receipt .= "• {$service->service_name}";
                $receipt .= str_repeat(' ', max(1, 25 - strlen($service->service_name)));
                $receipt .= "₱" . number_format($price, 2) . "\n";
            }
            $receipt .= "───────────────────────────────────\n";
            $receipt .= "Services Subtotal:         ₱" . number_format($servicesTotal, 2) . "\n\n";
        }
        
        // Products section
        if ($transaction->products->count() > 0) {
            $receipt .= "🧴 PRODUCTS\n";
            $receipt .= "───────────────────────────────────\n";
            $productsTotal = 0;
            foreach ($transaction->products as $product) {
                $price = $product->pivot->price_at_purchase ?? $product->price;
                $productsTotal += $price;
                $receipt .= "• {$product->product_name}";
                $receipt .= str_repeat(' ', max(1, 25 - strlen($product->product_name)));
                $receipt .= "₱" . number_format($price, 2) . "\n";
            }
            $receipt .= "───────────────────────────────────\n";
            $receipt .= "Products Subtotal:         ₱" . number_format($productsTotal, 2) . "\n\n";
        }
        
        // Total
        $receipt .= "═══════════════════════════════════\n";
        $receipt .= "💰 TOTAL:                  ₱" . number_format($transaction->total_price, 2) . "\n";
        $receipt .= "═══════════════════════════════════\n\n";
        
        // Notes
        if ($transaction->notes) {
            $receipt .= "📝 NOTES\n";
            $receipt .= "───────────────────────────────────\n";
            $receipt .= "{$transaction->notes}\n\n";
        }
        
        $receipt .= "Status: " . strtoupper($transaction->status) . "\n";
        $receipt .= "───────────────────────────────────\n";
        $receipt .= "Thank you for choosing WashHour!\n";
        
        return $receipt;
    }

    /**
     * Build event description from transaction details (legacy)
     */
    protected function buildEventDescription($transaction)
    {
        return $this->buildReceiptDescription($transaction);
    }

    /**
     * Legacy description builder
     */
    protected function buildSimpleDescription($transaction)
    {
        $description = "Laundry Pickup Booking\n\n";
        $description .= "Customer: {$transaction->user->fname} {$transaction->user->lname}\n";
        $description .= "Phone: {$transaction->user->phone}\n";
        $description .= "Email: {$transaction->user->email}\n";
        $description .= "Item Type: " . ucfirst($transaction->item_type) . "\n";
        $description .= "Pickup Address: {$transaction->pickup_address}\n";
        
        if ($transaction->services->count() > 0) {
            $description .= "\nServices:\n";
            foreach ($transaction->services as $service) {
                $description .= "- {$service->service_name}\n";
            }
        }
        
        if ($transaction->products->count() > 0) {
            $description .= "\nProducts:\n";
            foreach ($transaction->products as $product) {
                $description .= "- {$product->product_name}\n";
            }
        }
        
        $description .= "\nTotal: ₱" . number_format($transaction->total_price, 2);
        
        if ($transaction->notes) {
            $description .= "\n\nNotes: {$transaction->notes}";
        }
        
        return $description;
    }

    /**
     * Update a calendar event
     */
    public function updateEvent($eventId, $transaction)
    {
        try {
            $startDateTime = Carbon::parse($transaction->booking_date->format('Y-m-d') . ' ' . $transaction->booking_time);
            $endDateTime = $startDateTime->copy()->addHour();

            $serviceType = ucfirst($transaction->service_type ?? 'pickup');
            $payload = [
                'title' => "[{$serviceType}] {$transaction->user->username} - {$transaction->user->fname} {$transaction->user->lname}",
                'description' => $this->buildReceiptDescription($transaction),
                'start' => [
                    'date_time' => $startDateTime->toIso8601String(),
                    'time_zone' => $this->timezone,
                ],
                'end' => [
                    'date_time' => $endDateTime->toIso8601String(),
                    'time_zone' => $this->timezone,
                ],
                'location' => $transaction->pickup_address,
            ];

            $calendarId = $this->getActiveCalendarId();
            if (!$calendarId) {
                Log::error('No calendar ID available for update');
                return false;
            }

            $endpoint = "/calendars/{$calendarId}/events/{$eventId}";
            $response = $this->makeRequest('PATCH', $endpoint, $payload);

            if ($response->successful()) {
                Log::info('CalAPI event updated successfully', [
                    'event_id' => $eventId,
                ]);
                return true;
            }

            Log::error('CalAPI updateEvent failed', [
                'event_id' => $eventId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('CalAPI updateEvent exception', [
                'event_id' => $eventId,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Delete a calendar event
     */
    public function deleteEvent($eventId)
    {
        try {
            $calendarId = $this->getActiveCalendarId();
            if (!$calendarId) {
                Log::error('No calendar ID available for delete');
                return false;
            }

            $endpoint = "/calendars/{$calendarId}/events/{$eventId}";
            $response = $this->makeRequest('DELETE', $endpoint);

            if ($response->successful()) {
                Log::info('CalAPI event deleted successfully', [
                    'event_id' => $eventId,
                ]);
                return true;
            }

            Log::error('CalAPI deleteEvent failed', [
                'event_id' => $eventId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('CalAPI deleteEvent exception', [
                'event_id' => $eventId,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if a specific date and time slot is available
     */
    public function checkAvailability($date, $time)
    {
        $slots = $this->getAvailableSlots($date);

        foreach ($slots as $slot) {
            if ($slot['time'] === $time && $slot['available']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get booked slots for a specific date
     */
    public function getBookedSlots($date)
    {
        try {
            $calendarId = $this->getActiveCalendarId();
            if (!$calendarId) {
                return [];
            }

            $endpoint = "/calendars/{$calendarId}/events";
            $response = $this->makeRequest('GET', $endpoint, [
                'timeMin' => Carbon::parse($date)->startOfDay()->toIso8601String(),
                'timeMax' => Carbon::parse($date)->endOfDay()->toIso8601String(),
            ]);

            if ($response->successful()) {
                return $response->json('data', []);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('CalAPI getBookedSlots exception', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get default time slots (fallback when API fails)
     */
    protected function getDefaultTimeSlots()
    {
        $slots = [];
        $startHour = 8; // 8 AM
        $endHour = 18; // 6 PM

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $slots[] = [
                'time' => sprintf('%02d:00', $hour),
                'formatted' => Carbon::createFromTime($hour, 0)->format('g:i A'),
                'available' => true,
            ];
            $slots[] = [
                'time' => sprintf('%02d:30', $hour),
                'formatted' => Carbon::createFromTime($hour, 30)->format('g:i A'),
                'available' => true,
            ];
        }

        return $slots;
    }

    /**
     * Clear cache for a specific date
     */
    public function clearCache($date)
    {
        Cache::forget("calapi_slots_{$date}_60");
    }

    /**
     * Test method to create an event directly (for testing)
     */
    public function testCreateEvent($payload)
    {
        try {
            // First get the calendar ID
            $calendars = $this->getCalendars();
            
            if (empty($calendars)) {
                return [
                    'success' => false,
                    'message' => 'No calendars found. Please check your CalAPI.io setup.',
                ];
            }

            // Use the first calendar or the one specified in config
            $calendarId = $this->calendarId;
            
            // If using 'primary', find the actual primary calendar ID
            if ($calendarId === 'primary') {
                foreach ($calendars as $calendar) {
                    if (isset($calendar['primary']) && $calendar['primary']) {
                        $calendarId = $calendar['id'];
                        break;
                    }
                }
                
                // If no primary found, use first calendar
                if ($calendarId === 'primary' && !empty($calendars)) {
                    $calendarId = $calendars[0]['id'];
                }
            }

            Log::info('Test event creation', [
                'calendar_id' => $calendarId,
                'payload' => $payload,
            ]);
            
            // CalAPI.io endpoint: /calendars/{calendarId}/events
            $endpoint = "/calendars/{$calendarId}/events";
            $response = $this->makeRequest('POST', $endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $eventId = $data['id'] ?? null;
                
                Log::info('Test event created successfully', [
                    'event_id' => $eventId,
                    'calendar_id' => $calendarId,
                    'response' => $data,
                ]);
                
                return [
                    'success' => true,
                    'event_id' => $eventId,
                    'calendar_id' => $calendarId,
                    'data' => $data,
                ];
            }

            Log::error('Test event creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'API returned: ' . $response->status(),
                'body' => $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Test event creation exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
