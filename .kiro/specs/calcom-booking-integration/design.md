# Design Document: Cal.com Booking Integration

## Overview

This design document outlines the architecture and implementation strategy for integrating Cal.com as a calendar and booking system within the Laravel application. The integration will provide role-based booking management where users can create and manage their own bookings, while administrators have oversight of all bookings across the system.

The solution leverages Cal.com's API for booking operations and implements a service-oriented architecture following the existing application patterns. The design emphasizes security, maintainability, and clear separation of concerns between user and administrator functionality.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      Presentation Layer                      │
│  ┌──────────────────────┐      ┌──────────────────────┐    │
│  │  User Booking View   │      │ Admin Booking View   │    │
│  │  (Blade Template)    │      │  (Blade Template)    │    │
│  └──────────────────────┘      └──────────────────────┘    │
│           │                              │                   │
│           │                              │                   │
│  ┌────────▼──────────────────────────────▼────────────┐    │
│  │           JavaScript Booking Manager                │    │
│  │     (AJAX requests, UI updates, validation)         │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
                           │
                           │ HTTP/AJAX
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                     Controller Layer                         │
│  ┌──────────────────────┐      ┌──────────────────────┐    │
│  │  UserController      │      │  AdminController     │    │
│  │  - viewBookings()    │      │  - viewAllBookings() │    │
│  │  - createBooking()   │      │  - manageBooking()   │    │
│  │  - updateBooking()   │      │  - filterBookings()  │    │
│  │  - cancelBooking()   │      │  - searchBookings()  │    │
│  └──────────────────────┘      └──────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
                           │
                           │ Service Calls
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                      Service Layer                           │
│  ┌───────────────────────────────────────────────────┐      │
│  │            CalComService                          │      │
│  │  - createBooking(userId, bookingData)             │      │
│  │  - getUserBookings(userId)                        │      │
│  │  - getAllBookings()                               │      │
│  │  - updateBooking(bookingId, data)                 │      │
│  │  - cancelBooking(bookingId)                       │      │
│  │  - searchBookings(filters)                        │      │
│  └───────────────────────────────────────────────────┘      │
│                           │                                  │
│                           │ API Calls                        │
│                           ▼                                  │
│  ┌───────────────────────────────────────────────────┐      │
│  │         HTTP Client (Guzzle)                      │      │
│  │  - Authentication headers                         │      │
│  │  - Request/Response transformation                │      │
│  │  - Error handling & retry logic                   │      │
│  └───────────────────────────────────────────────────┘      │
└─────────────────────────────────────────────────────────────┘
                           │
                           │ HTTPS
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                    Cal.com API                               │
│  - Booking endpoints                                         │
│  - Event type management                                     │
│  - User/attendee management                                  │
└─────────────────────────────────────────────────────────────┘
```

### Database Schema

```sql
-- Bookings table to cache Cal.com data and maintain relationships
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    calcom_booking_id VARCHAR(255) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'rescheduled') DEFAULT 'pending',
    attendee_email VARCHAR(255) NULL,
    attendee_name VARCHAR(255) NULL,
    attendee_phone VARCHAR(50) NULL,
    location VARCHAR(255) NULL,
    notes TEXT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_start_time (start_time)
);
```

## Components and Interfaces

### 1. CalComService

The central service class responsible for all Cal.com API interactions.

```php
namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CalComService
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.calcom.api_key');
        $this->baseUrl = config('services.calcom.base_url');
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }
    
    /**
     * Create a new booking
     * 
     * @param int $userId
     * @param array $bookingData
     * @return array
     */
    public function createBooking(int $userId, array $bookingData): array;
    
    /**
     * Get all bookings for a specific user
     * 
     * @param int $userId
     * @return array
     */
    public function getUserBookings(int $userId): array;
    
    /**
     * Get all bookings (admin only)
     * 
     * @return array
     */
    public function getAllBookings(): array;
    
    /**
     * Update an existing booking
     * 
     * @param string $bookingId
     * @param array $data
     * @return array
     */
    public function updateBooking(string $bookingId, array $data): array;
    
    /**
     * Cancel a booking
     * 
     * @param string $bookingId
     * @param string $reason
     * @return bool
     */
    public function cancelBooking(string $bookingId, string $reason = ''): bool;
    
    /**
     * Search bookings with filters
     * 
     * @param array $filters
     * @return array
     */
    public function searchBookings(array $filters): array;
}
```

### 2. Booking Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'calcom_booking_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'status',
        'attendee_email',
        'attendee_name',
        'attendee_phone',
        'location',
        'notes',
        'metadata',
    ];
    
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'metadata' => 'array',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
                    ->where('status', '!=', 'cancelled');
    }
    
    public function scopePast($query)
    {
        return $query->where('end_time', '<', now());
    }
}
```

### 3. Controller Methods

**UserController additions:**
```php
public function viewBookings()
{
    // Display user's booking view
}

public function getUserBookingsData()
{
    // AJAX endpoint to fetch user's bookings
}

public function createBooking(Request $request)
{
    // Create new booking for authenticated user
}

public function updateBooking(Request $request, $bookingId)
{
    // Update user's own booking
}

public function cancelBooking($bookingId)
{
    // Cancel user's own booking
}
```

**AdminController additions:**
```php
public function viewAllBookings()
{
    // Display admin booking management view
}

public function getAllBookingsData(Request $request)
{
    // AJAX endpoint with filtering/search
}

public function manageBooking(Request $request, $bookingId)
{
    // Update any user's booking
}

public function cancelUserBooking($bookingId, Request $request)
{
    // Cancel any user's booking with notification
}

public function searchBookings(Request $request)
{
    // Search bookings with filters
}
```

### 4. Request Validation Classes

```php
namespace App\Http\Requests\User;

class CreateBooking extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'attendee_email' => 'nullable|email',
            'attendee_name' => 'nullable|string|max:255',
            'attendee_phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }
}
```

## Data Models

### Booking Data Structure

```php
[
    'id' => 'integer',
    'user_id' => 'integer',
    'calcom_booking_id' => 'string',
    'title' => 'string',
    'description' => 'string|null',
    'start_time' => 'datetime',
    'end_time' => 'datetime',
    'status' => 'enum(pending|confirmed|cancelled|rescheduled)',
    'attendee_email' => 'string|null',
    'attendee_name' => 'string|null',
    'attendee_phone' => 'string|null',
    'location' => 'string|null',
    'notes' => 'string|null',
    'metadata' => 'json|null',
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp',
]
```

### Cal.com API Response Transformation

The service will transform Cal.com API responses into the application's booking format:

```php
private function transformCalComBooking(array $calcomData): array
{
    return [
        'calcom_booking_id' => $calcomData['id'],
        'title' => $calcomData['title'] ?? 'Booking',
        'description' => $calcomData['description'] ?? null,
        'start_time' => $calcomData['startTime'],
        'end_time' => $calcomData['endTime'],
        'status' => $this->mapCalComStatus($calcomData['status']),
        'attendee_email' => $calcomData['attendees'][0]['email'] ?? null,
        'attendee_name' => $calcomData['attendees'][0]['name'] ?? null,
        'location' => $calcomData['location'] ?? null,
        'metadata' => $calcomData,
    ];
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*


### Property Reflection

After reviewing all testable properties from the prework analysis, several redundancies were identified:

- Properties 2.3 and 7.3 both test user filtering - these will be combined
- Properties 2.4 and 8.4 both test admin modification notifications - these will be combined
- Properties 1.3 and 4.3 both test booking retrieval completeness - these will be combined
- Edge cases 6.3, 6.4, and 6.5 will be handled by property 6.1 which tests all status displays

The consolidated properties below eliminate redundancy while maintaining comprehensive coverage.

### Correctness Properties

Property 1: User booking isolation
*For any* user and set of bookings in the system, fetching bookings for that user should return only bookings where the user_id matches that user's ID
**Validates: Requirements 1.1**

Property 2: Booking creation persistence
*For any* valid booking data and user, creating a booking should result in a booking record that exists in the database and is associated with the correct user_id
**Validates: Requirements 1.2**

Property 3: Booking retrieval completeness
*For any* user with multiple bookings, fetching that user's bookings should return all bookings associated with that user
**Validates: Requirements 1.3, 4.3**

Property 4: Booking update identity preservation
*For any* existing booking and valid update data, updating the booking should preserve the booking ID and user association while changing the specified fields
**Validates: Requirements 1.4**

Property 5: Booking cancellation removes from active list
*For any* active booking, cancelling it should result in the booking either being removed from the active bookings list or having its status set to 'cancelled'
**Validates: Requirements 1.5**

Property 6: Admin access to all bookings
*For any* set of bookings across multiple users, an admin fetch operation should return all bookings regardless of which user they belong to
**Validates: Requirements 2.1**

Property 7: Booking details include user information
*For any* booking retrieved by an admin, the booking data should include the associated user's information (at minimum user_id, and preferably name/email)
**Validates: Requirements 2.2**

Property 8: User filter returns only matching bookings
*For any* user ID used as a filter, the filtered results should contain only bookings where the user_id matches the filter value
**Validates: Requirements 2.3, 7.3**

Property 9: Admin modification triggers notification
*For any* booking modified by an admin, a notification should be sent to the user who owns that booking
**Validates: Requirements 2.4, 8.4**

Property 10: Admin cancellation triggers notification
*For any* booking cancelled by an admin, a notification should be sent to the user who owns that booking
**Validates: Requirements 2.5**

Property 11: API calls include authentication
*For any* API request made to Cal.com, the request headers should include valid authentication credentials
**Validates: Requirements 3.1**

Property 12: API errors return graceful responses
*For any* API request that fails, the system should return a structured error response with a meaningful message rather than throwing an unhandled exception
**Validates: Requirements 3.3**

Property 13: Rate limit triggers retry logic
*For any* API request that receives a rate limit response (429), the system should implement retry logic with exponential backoff
**Validates: Requirements 3.4**

Property 14: User role restricts admin operations
*For any* user with the 'user' role, attempting to perform admin-only operations (like viewing all bookings or modifying other users' bookings) should be denied
**Validates: Requirements 4.5**

Property 15: Cal.com response transformation
*For any* valid Cal.com API response, the transformation function should produce a booking object with all required application fields (user_id, title, start_time, end_time, status)
**Validates: Requirements 5.2**

Property 16: Service errors are logged and standardized
*For any* error occurring in the CalComService, the error should be logged and the returned error response should follow the standard application error format
**Validates: Requirements 5.3**

Property 17: Booking display includes required fields
*For any* booking displayed to a user, the output should include date, time, duration, and status information
**Validates: Requirements 6.1**

Property 18: Attendee information displayed when present
*For any* booking with attendee data, the display should include attendee name, email, or phone information
**Validates: Requirements 6.2**

Property 19: Search criteria filters results correctly
*For any* search criteria applied to bookings, all returned results should match the specified criteria
**Validates: Requirements 7.1**

Property 20: Date range filter returns only bookings in range
*For any* date range filter, all returned bookings should have start_time within the specified range
**Validates: Requirements 7.2**

Property 21: Status filter returns only matching status
*For any* status filter value, all returned bookings should have a status matching the filter value
**Validates: Requirements 7.4**

Property 22: Clearing filters restores full list
*For any* filtered booking list, clearing all filters should return the complete unfiltered list of bookings
**Validates: Requirements 7.5**

Property 23: Booking creation triggers notification
*For any* newly created booking, a confirmation notification should be sent to the user who created it
**Validates: Requirements 8.1**

Property 24: Booking update triggers notification
*For any* updated booking, an update notification should be sent to the user who owns the booking
**Validates: Requirements 8.2**

Property 25: Booking cancellation triggers notification
*For any* cancelled booking, a cancellation notification should be sent to the user who owns the booking
**Validates: Requirements 8.3**

## Error Handling

### API Error Handling

The CalComService will implement comprehensive error handling for Cal.com API interactions:

1. **Network Errors**: Catch connection timeouts and network failures, return user-friendly error messages
2. **Authentication Errors (401)**: Log authentication failures, check API key validity, return auth error response
3. **Rate Limiting (429)**: Implement exponential backoff retry logic (3 attempts), notify user if all retries fail
4. **Not Found (404)**: Handle missing bookings gracefully, return appropriate error message
5. **Validation Errors (422)**: Parse Cal.com validation errors, return field-specific error messages
6. **Server Errors (500+)**: Log server errors, return generic error message to user, implement retry for transient failures

### Error Response Format

All errors will follow the application's standard error response format:

```php
[
    'success' => false,
    'message' => 'Human-readable error message',
    'errors' => [
        'field_name' => ['Specific validation error'],
    ],
    'code' => 'ERROR_CODE',
]
```

### Logging Strategy

- Log all API requests and responses at INFO level (excluding sensitive data)
- Log all errors at ERROR level with full context
- Log retry attempts at WARNING level
- Include correlation IDs for request tracing

## Testing Strategy

### Unit Testing

Unit tests will verify individual components in isolation:

1. **CalComService Unit Tests**:
   - Test API request construction with mocked HTTP client
   - Test response transformation logic
   - Test error handling for various API responses
   - Test authentication header inclusion

2. **Booking Model Tests**:
   - Test model relationships (user association)
   - Test scopes (upcoming, past bookings)
   - Test attribute casting (dates, JSON)

3. **Controller Tests**:
   - Test request validation
   - Test authorization (users can only access own bookings)
   - Test response formatting

4. **Request Validation Tests**:
   - Test validation rules for booking creation
   - Test validation rules for booking updates
   - Test edge cases (past dates, invalid time ranges)

### Property-Based Testing

Property-based tests will verify universal properties across many random inputs using a PHP property testing library (e.g., Eris or php-quickcheck):

**Testing Framework**: We will use **Eris** (PHP port of QuickCheck) for property-based testing. Each property test will run a minimum of 100 iterations with randomly generated data.

**Key Properties to Test**:

1. **User Isolation Property**: Generate random users and bookings, verify users only see their own bookings
2. **Booking CRUD Round-Trip**: Create random booking data, persist it, retrieve it, verify data integrity
3. **Filter Consistency**: Generate random bookings and filter criteria, verify filtered results always match criteria
4. **Admin Access Scope**: Generate bookings for multiple users, verify admin always sees all bookings
5. **Transformation Idempotency**: Transform Cal.com responses multiple times, verify consistent output
6. **Date Range Filtering**: Generate bookings with random dates, verify range filters always return correct subset

**Property Test Tagging**: Each property-based test will include a comment tag in this format:
```php
/**
 * Feature: calcom-booking-integration, Property 1: User booking isolation
 * Validates: Requirements 1.1
 */
```

### Integration Testing

Integration tests will verify end-to-end workflows:

1. **Booking Creation Flow**: Test complete flow from user input to Cal.com API call to database persistence
2. **Admin Management Flow**: Test admin viewing all bookings, filtering, and modifying user bookings
3. **Notification Flow**: Test that booking operations trigger appropriate notifications
4. **Authentication Flow**: Test that unauthenticated requests are rejected

### Manual Testing Checklist

- Verify Cal.com API credentials are correctly configured
- Test booking creation through UI
- Test booking cancellation through UI
- Verify admin can see all users' bookings
- Verify users cannot see other users' bookings
- Test error handling with invalid API credentials
- Test responsive design on mobile devices

## Security Considerations

1. **API Key Protection**: Store Cal.com API key in `.env` file, never commit to version control
2. **Authorization**: Implement middleware to verify users can only access their own bookings
3. **Input Validation**: Validate all user input before sending to Cal.com API
4. **SQL Injection Prevention**: Use Eloquent ORM with parameter binding
5. **XSS Prevention**: Escape all output in Blade templates
6. **CSRF Protection**: Use Laravel's CSRF token for all forms
7. **Rate Limiting**: Implement rate limiting on booking endpoints to prevent abuse

## Performance Considerations

1. **Caching**: Cache Cal.com API responses for 5 minutes to reduce API calls
2. **Pagination**: Implement pagination for booking lists (20 bookings per page)
3. **Lazy Loading**: Use lazy loading for booking relationships
4. **Database Indexing**: Index user_id, status, and start_time columns for fast queries
5. **Async Operations**: Queue notification sending to avoid blocking booking operations

## Deployment Considerations

1. **Environment Variables**: Add Cal.com API credentials to `.env` and `.env.example`
2. **Database Migration**: Run migration to create bookings table
3. **API Key Setup**: Obtain Cal.com API key and configure in production environment
4. **Monitoring**: Set up monitoring for Cal.com API errors and response times
5. **Rollback Plan**: Maintain ability to disable Cal.com integration via feature flag

## Future Enhancements

1. **Calendar Sync**: Sync bookings with external calendars (Google Calendar, Outlook)
2. **Recurring Bookings**: Support for recurring appointment patterns
3. **Booking Templates**: Allow users to create booking templates for common appointment types
4. **Analytics Dashboard**: Provide booking analytics and insights for administrators
5. **Mobile App**: Develop mobile application for booking management
6. **Webhook Integration**: Implement Cal.com webhooks for real-time booking updates
