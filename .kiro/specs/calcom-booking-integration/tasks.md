# Implementation Plan: Cal.com Booking Integration

- [x] 1. Set up Cal.com configuration and service foundation





  - Add Cal.com API credentials to `.env.example` and configuration
  - Create `config/services.php` entry for Cal.com settings
  - Install Guzzle HTTP client if not already present
  - _Requirements: 3.1, 3.2, 5.4_

- [x] 2. Create database schema and Booking model





  - Create migration for `bookings` table with all required fields
  - Implement Booking model with fillable fields, casts, and relationships
  - Add `user()` relationship method to Booking model
  - Add `bookings()` relationship method to User model
  - Implement `upcoming()` and `past()` query scopes on Booking model
  - _Requirements: 1.1, 1.2, 6.1_



- [x] 3. Implement CalComService for API integration



  - Create `app/Services/CalComService.php` with constructor and HTTP client setup
  - Implement `createBooking()` method with API call and error handling
  - Implement `getUserBookings()` method to fetch user-specific bookings
  - Implement `getAllBookings()` method for admin access
  - Implement `updateBooking()` method with API call
  - Implement `cancelBooking()` method with API call
  - Implement `searchBookings()` method with filter support
  - Implement private `transformCalComBooking()` method for response transformation
  - Implement private `handleApiError()` method for error handling and logging
  - Implement retry logic with exponential backoff for rate limiting
  - _Requirements: 3.1, 3.3, 3.4, 3.5, 5.1, 5.2, 5.3_

- [ ]* 3.1 Write property test for API authentication
  - **Property 11: API calls include authentication**
  - **Validates: Requirements 3.1**

- [ ]* 3.2 Write property test for Cal.com response transformation
  - **Property 15: Cal.com response transformation**
  - **Validates: Requirements 5.2**

- [ ]* 3.3 Write property test for error handling
  - **Property 12: API errors return graceful responses**
  - **Validates: Requirements 3.3**

- [ ]* 3.4 Write property test for rate limit retry logic
  - **Property 13: Rate limit triggers retry logic**
  - **Validates: Requirements 3.4**

- [ ]* 3.5 Write property test for service error logging
  - **Property 16: Service errors are logged and standardized**
  - **Validates: Requirements 5.3**

- [x] 4. Create request validation classes





  - Create `app/Http/Requests/User/CreateBooking.php` with validation rules
  - Create `app/Http/Requests/User/UpdateBooking.php` with validation rules
  - Create `app/Http/Requests/Admin/ManageBooking.php` with validation rules
  - Create `app/Http/Requests/Admin/SearchBookings.php` with validation rules
  - _Requirements: 1.2, 1.4, 2.4, 7.1_

- [ ]* 4.1 Write unit tests for request validation
  - Test CreateBooking validation rules
  - Test UpdateBooking validation rules
  - Test date/time validation edge cases
  - _Requirements: 1.2, 1.4_

- [x] 5. Implement user booking controller methods





  - Add `viewBookings()` method to UserController to render booking view
  - Add `getUserBookingsData()` method to fetch user's bookings via AJAX
  - Add `createBooking()` method to create new booking for authenticated user
  - Add `updateBooking()` method to update user's own booking
  - Add `cancelBooking()` method to cancel user's own booking
  - Implement authorization checks to ensure users only access their own bookings
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 4.1, 4.5_

- [ ]* 5.1 Write property test for user booking isolation
  - **Property 1: User booking isolation**
  - **Validates: Requirements 1.1**

- [ ]* 5.2 Write property test for booking creation persistence
  - **Property 2: Booking creation persistence**
  - **Validates: Requirements 1.2**

- [ ]* 5.3 Write property test for booking retrieval completeness
  - **Property 3: Booking retrieval completeness**
  - **Validates: Requirements 1.3, 4.3**

- [ ]* 5.4 Write property test for booking update identity preservation
  - **Property 4: Booking update identity preservation**
  - **Validates: Requirements 1.4**

- [ ]* 5.5 Write property test for booking cancellation
  - **Property 5: Booking cancellation removes from active list**
  - **Validates: Requirements 1.5**

- [ ]* 5.6 Write property test for user role restrictions
  - **Property 14: User role restricts admin operations**
  - **Validates: Requirements 4.5**
-

- [x] 6. Implement admin booking controller methods




  - Add `viewAllBookings()` method to AdminController to render admin booking view
  - Add `getAllBookingsData()` method to fetch all bookings with filtering via AJAX
  - Add `manageBooking()` method to update any user's booking
  - Add `cancelUserBooking()` method to cancel any user's booking
  - Add `searchBookings()` method to search bookings with filters
  - Implement filter logic for user, date range, and status filters
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 7.1, 7.2, 7.3, 7.4, 7.5_

- [ ]* 6.1 Write property test for admin access to all bookings
  - **Property 6: Admin access to all bookings**
  - **Validates: Requirements 2.1**

- [ ]* 6.2 Write property test for booking details include user info
  - **Property 7: Booking details include user information**
  - **Validates: Requirements 2.2**

- [ ]* 6.3 Write property test for user filter
  - **Property 8: User filter returns only matching bookings**
  - **Validates: Requirements 2.3, 7.3**

- [ ]* 6.4 Write property test for search criteria filtering
  - **Property 19: Search criteria filters results correctly**
  - **Validates: Requirements 7.1**

- [ ]* 6.5 Write property test for date range filtering
  - **Property 20: Date range filter returns only bookings in range**
  - **Validates: Requirements 7.2**

- [ ]* 6.6 Write property test for status filtering
  - **Property 21: Status filter returns only matching status**
  - **Validates: Requirements 7.4**

- [ ]* 6.7 Write property test for clearing filters
  - **Property 22: Clearing filters restores full list**
  - **Validates: Requirements 7.5**

- [x] 7. Add routes for booking functionality




  - Add user booking routes (view, create, update, cancel) with authentication middleware
  - Add admin booking routes (view all, manage, search) with admin middleware
  - Ensure proper route naming for easy reference
  - _Requirements: 4.1, 4.2, 4.4_

- [x] 8. Create user booking view (Blade template)





  - Create `resources/views/user/bookings.blade.php` with layout
  - Add booking list display section with date, time, status
  - Add create booking form modal
  - Add edit booking modal
  - Add cancel booking confirmation modal
  - Include navigation link in user nav menu
  - _Requirements: 1.1, 1.3, 4.1, 6.1, 6.2_

- [ ]* 8.1 Write property test for booking display fields
  - **Property 17: Booking display includes required fields**
  - **Validates: Requirements 6.1**

- [ ]* 8.2 Write property test for attendee information display
  - **Property 18: Attendee information displayed when present**
  - **Validates: Requirements 6.2**

- [x] 9. Create admin booking view (Blade template)




  - Create `resources/views/admin/bookings.blade.php` with layout
  - Add comprehensive booking list with user information column
  - Add search and filter controls (user, date range, status)
  - Add edit booking modal for any user's booking
  - Add cancel booking modal with reason input
  - Include navigation link in admin nav menu
  - _Requirements: 2.1, 2.2, 2.3, 4.2, 7.1, 7.2, 7.3, 7.4, 7.5_

- [-] 10. Implement frontend JavaScript for user bookings


  - Create `resources/js/user-bookings.js` for booking management
  - Implement AJAX call to fetch user bookings on page load
  - Implement create booking form submission with validation
  - Implement edit booking form submission
  - Implement cancel booking with confirmation
  - Add real-time UI updates after booking operations
  - Add error handling and user feedback notifications
  - _Requirements: 1.2, 1.3, 1.4, 1.5, 4.3_

- [ ] 11. Implement frontend JavaScript for admin bookings
  - Create `resources/js/admin-bookings.js` for booking management
  - Implement AJAX call to fetch all bookings with filters
  - Implement search functionality with debouncing
  - Implement filter controls (user dropdown, date range picker, status dropdown)
  - Implement clear filters functionality
  - Implement edit booking modal for any user
  - Implement cancel booking with reason and user notification
  - Add real-time UI updates after booking operations
  - _Requirements: 2.1, 2.3, 2.4, 2.5, 7.1, 7.2, 7.3, 7.4, 7.5_

- [ ] 12. Integrate notification system for booking events
  - Extend MessageService or create BookingNotificationService
  - Implement notification for booking creation
  - Implement notification for booking update
  - Implement notification for booking cancellation
  - Implement notification for admin modifications to user bookings
  - Add notification calls to appropriate controller methods
  - _Requirements: 8.1, 8.2, 8.3, 8.4_

- [ ]* 12.1 Write property test for creation notification
  - **Property 23: Booking creation triggers notification**
  - **Validates: Requirements 8.1**

- [ ]* 12.2 Write property test for update notification
  - **Property 24: Booking update triggers notification**
  - **Validates: Requirements 8.2**

- [ ]* 12.3 Write property test for cancellation notification
  - **Property 25: Booking cancellation triggers notification**
  - **Validates: Requirements 8.3**

- [ ]* 12.4 Write property test for admin modification notification
  - **Property 9: Admin modification triggers notification**
  - **Validates: Requirements 2.4, 8.4**

- [ ]* 12.5 Write property test for admin cancellation notification
  - **Property 10: Admin cancellation triggers notification**
  - **Validates: Requirements 2.5**

- [ ] 13. Add caching layer for Cal.com API responses
  - Implement cache wrapper in CalComService for getUserBookings()
  - Implement cache wrapper for getAllBookings()
  - Set cache TTL to 5 minutes
  - Implement cache invalidation on booking create/update/cancel
  - _Requirements: Performance optimization_

- [ ] 14. Implement authorization middleware checks
  - Verify isUser middleware prevents access to admin booking routes
  - Verify isAdmin middleware allows access to admin booking routes
  - Add authorization checks in controller methods for booking ownership
  - _Requirements: 4.4, 4.5_

- [ ] 15. Add database indexes for performance
  - Add index on bookings.user_id column
  - Add index on bookings.status column
  - Add index on bookings.start_time column
  - Add composite index on (user_id, start_time) for common queries
  - _Requirements: Performance optimization_

- [ ] 16. Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 17. Create seed data for development and testing
  - Add booking factory for generating test bookings
  - Create seeder to populate sample bookings for existing users
  - _Requirements: Development support_

- [ ]* 17.1 Write integration tests for booking workflows
  - Test complete user booking creation flow
  - Test complete admin booking management flow
  - Test authentication and authorization flows
  - _Requirements: 1.1, 1.2, 2.1, 2.4, 4.4_

- [ ] 18. Update documentation
  - Add Cal.com setup instructions to README
  - Document environment variables needed
  - Document API endpoints and their usage
  - Add inline code documentation for CalComService methods
  - _Requirements: Documentation_

- [ ] 19. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
