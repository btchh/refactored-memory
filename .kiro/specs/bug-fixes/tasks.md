# Implementation Plan

- [x] 1. Fix Model Naming Conventions





  - Rename `app/Models/productTransactions.php` to `app/Models/ProductTransaction.php`
  - Rename `app/Models/serviceTransactions.php` to `app/Models/ServiceTransaction.php`
  - Update any imports if necessary
  - Clear Laravel's cached class map
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 2. Fix Password Hashing Issues





  - Remove `setPasswordAttribute` mutator from Admin model
  - Verify User model uses 'hashed' cast correctly
  - Remove `Hash::make()` call in AdminController's `resetPassword` method
  - Update AdminService to not hash passwords (let model handle it)
  - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [x] 3. Fix Transaction Total Price Calculation




- [x] 3.1 Create TransactionObserver class


  - Create `app/Observers/TransactionObserver.php`
  - Implement `saved` method to calculate total price
  - Handle both new and updated transactions
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_



- [x] 3.2 Register TransactionObserver





  - Update `AppServiceProvider` to register the observer


  - Remove `boot` method from Transaction model
  - _Requirements: 3.1_

- [x] 3.3 Update Transaction model




  - Remove `boot` method and `saving` event
  - Keep `calculateTotalPrice` method for observer to use
  - _Requirements: 3.1, 3.4_

- [x] 4. Fix Typo in AuthService




  - Rename `$feildType` to `$fieldType` in both `loginUser` and `loginAdmin` methods
  - _Requirements: 13.1, 13.2, 13.3_

- [x] 5. Add Null Safety to AdminService




  - Add null check at start of `changePass` method
  - Throw descriptive exception if admin is null
  - _Requirements: 8.1, 8.2, 8.3_

- [x] 6. Fix User Registration Race Conditions





  - Wrap user creation in database transaction in UserService
  - Re-validate uniqueness constraints inside transaction
  - Add proper error handling for constraint violations
  - _Requirements: 9.1, 9.2, 9.3, 9.4_

- [x] 7. Remove Debug Code from Production





  - Remove debug OTP exposure in UserController's `sendRegistrationOtp` method
  - Remove debug OTP exposure in UserController's `sendPasswordResetOtp` method
  - Ensure debug checks use `config('app.debug')` properly
  - _Requirements: 11.1, 11.2, 11.3_

- [x] 8. Improve Geocoding Error Handling





  - Ensure geocoding failures return null instead of throwing exceptions
  - Add null checks before attempting route calculations
  - Update controllers to handle null coordinates gracefully
  - _Requirements: 7.1, 7.2, 7.3, 7.4_

- [x] 9. Fix OTP Verification in Registration





  - Verify OTP verification cache is checked in UserController's `register` method
  - Add proper error handling if OTP not verified
  - Ensure verification cache is cleared after successful registration
  - _Requirements: 5.1, 5.2, 5.3_

- [x] 10. Improve API Error Response Consistency






  - Review all API endpoints for consistent error response format
  - Ensure all responses include 'success' boolean
  - Use appropriate HTTP status codes
  - _Requirements: 10.1, 10.2, 10.3, 10.4_

- [x] 11. Add Query Performance Optimizations









  - Add eager loading to admin location queries in UserController
  - Review other potential N+1 query locations
  - Add database query logging in development to identify issues
  - _Requirements: 4.1, 4.2, 4.3_

- [x] 12. Fix Admin Change Password Route Naming





  - Update the POST route for admin change password to use `->name('update-password')` instead of relying on the prefix
  - Verify the route name matches the view reference `route('admin.update-password')`
  - Test that the change password form submits successfully
  - _Requirements: 14.1, 14.2, 14.3, 14.4_

- [ ] 13. Checkpoint - Verify All Fixes
  - Ensure all tests pass, ask the user if questions arise
  - Test model renaming with autoloader
  - Test password hashing for both users and admins
  - Test transaction total calculation
  - Test OTP verification flow
  - Test geocoding error handling
  - Test concurrent registration attempts
  - Test admin change password route
