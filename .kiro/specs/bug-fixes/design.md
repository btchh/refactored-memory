# Design Document

## Overview

This design document outlines the approach to fix 13 identified bugs and issues in the Laravel waste management application. The fixes address naming conventions, security vulnerabilities, performance issues, and code quality problems.

## Architecture

The fixes will be applied across multiple layers:

1. **Model Layer**: Fix naming conventions, password hashing, and model event handling
2. **Service Layer**: Improve null safety and error handling
3. **Controller Layer**: Enhance validation and error responses
4. **Database Layer**: No schema changes required
5. **Configuration Layer**: No changes required

## Components and Interfaces

### 1. Model Files

**Files to Rename:**
- `app/Models/productTransactions.php` → `app/Models/ProductTransaction.php`
- `app/Models/serviceTransactions.php` → `app/Models/ServiceTransaction.php`

**Password Hashing Changes:**
- Remove `setPasswordAttribute` mutator from `Admin` model (conflicts with 'hashed' cast)
- Ensure `User` model uses 'hashed' cast consistently
- Update controllers to not double-hash passwords

### 2. Transaction Model

**Current Issue:** The `saving` event fires before relationships are attached, causing `calculateTotalPrice()` to return 0.

**Solution:** Use a Model Observer pattern instead of boot method:
- Create `TransactionObserver` class
- Move calculation logic to `saved` event (fires after save completes)
- Register observer in `AppServiceProvider`

### 3. Service Classes

**AdminService Changes:**
- Add null check in `changePass` method before using `$admin`
- Throw descriptive exception when admin is null

**UserService Changes:**
- Add database transaction wrapper for `createUser` to prevent race conditions
- Re-validate uniqueness constraints inside transaction

### 4. Controller Classes

**AdminController Changes:**
- Remove double hashing in `resetPassword` method
- Add proper null checks before geocoding
- Improve error response consistency

**UserController Changes:**
- Remove debug OTP exposure in production
- Add proper validation for concurrent registrations

## Data Models

No changes to database schema required. All fixes are code-level only.

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Model File Name Consistency
*For any* model class, the file name should match the class name exactly in PascalCase format.
**Validates: Requirements 1.1, 1.2**

### Property 2: Password Hashing Idempotency
*For any* password value, hashing it once should produce the same result as the system's password verification, and hashing it twice should fail verification.
**Validates: Requirements 2.1, 2.2, 2.3, 2.4**

### Property 3: Transaction Total Accuracy
*For any* transaction with attached products and services, the total_price should equal the sum of all price_at_purchase values from both pivot tables.
**Validates: Requirements 3.1, 3.2, 3.3, 3.4**

### Property 4: Zero Total for Empty Transactions
*For any* transaction with no products and no services, the total_price should be zero.
**Validates: Requirements 3.5**

### Property 5: Query Count Optimization
*For any* operation that loads multiple related models, the number of database queries should not increase linearly with the number of records (no N+1 queries).
**Validates: Requirements 4.1, 4.2, 4.3**

### Property 6: OTP Verification Enforcement
*For any* user registration attempt, if OTP verification has not been completed, the account creation should fail.
**Validates: Requirements 5.1, 5.3**

### Property 7: OTP Single Use
*For any* OTP verification that succeeds, subsequent attempts to use the same verification should fail.
**Validates: Requirements 5.2**

### Property 8: Token Expiration Enforcement
*For any* password reset token older than 60 minutes, the system should reject it.
**Validates: Requirements 6.2, 6.3**

### Property 9: Token Cleanup
*For any* successful password reset, the used token should be removed from the database.
**Validates: Requirements 6.4**

### Property 10: Graceful Geocoding Failure
*For any* address that fails to geocode, the system should continue operation with null coordinates rather than throwing an exception.
**Validates: Requirements 7.1, 7.2, 7.4**

### Property 11: Route Calculation Precondition
*For any* route calculation request, if either the admin or user lacks coordinates, the system should return an error rather than attempting calculation.
**Validates: Requirements 7.3**

### Property 12: Null Admin Handling
*For any* AdminService method that receives a null admin parameter, the system should throw a descriptive exception before attempting to use the admin object.
**Validates: Requirements 8.1, 8.2, 8.3**

### Property 13: Registration Uniqueness
*For any* two concurrent registration attempts with the same email, phone, or username, only one should succeed.
**Validates: Requirements 9.1, 9.2, 9.3, 9.4**

### Property 14: API Response Structure
*For any* API endpoint response, it should contain a "success" boolean field and appropriate HTTP status code.
**Validates: Requirements 10.1, 10.2, 10.4**

### Property 15: Production Debug Removal
*For any* production environment, debug information including OTP codes should not be included in responses.
**Validates: Requirements 11.1, 11.2, 11.3**

### Property 16: Route Name Consistency
*For any* route reference in a view, the corresponding route definition must exist with the exact same name.
**Validates: Requirements 14.1, 14.2, 14.3, 14.4**

## Error Handling

### Geocoding Failures
- Log errors but continue execution
- Set coordinates to null when geocoding fails
- Prevent route calculation when coordinates are missing

### Database Errors
- Use database transactions for critical operations
- Provide clear error messages for constraint violations
- Roll back on failure

### Validation Errors
- Return consistent JSON error responses
- Include field-specific error messages
- Use appropriate HTTP status codes (422 for validation, 400 for bad requests)

### Null Reference Errors
- Add null checks before using potentially null objects
- Throw descriptive exceptions early
- Avoid silent failures

## Testing Strategy

### Unit Tests
- Test password hashing with both User and Admin models
- Test transaction total calculation with various product/service combinations
- Test OTP verification flow
- Test null handling in services
- Test geocoding failure scenarios

### Integration Tests
- Test complete user registration flow with OTP
- Test admin password reset flow
- Test transaction creation with products and services
- Test concurrent registration attempts

### Manual Testing
- Verify model file renaming doesn't break autoloading
- Test geocoding with real addresses
- Verify debug code is removed in production
- Test route calculation with missing coordinates

## Implementation Notes

### File Renaming
When renaming model files, ensure:
1. Git tracks the rename (use `git mv` command)
2. All imports are updated
3. No cached references exist

### Password Hashing
The 'hashed' cast in Laravel 11 automatically handles password hashing. Do not combine with manual mutators.

### Transaction Observers
Observers provide cleaner separation of concerns than boot methods and fire at the correct lifecycle points.

### Database Transactions
Use `DB::transaction()` for operations that must be atomic, especially when checking uniqueness constraints.

### Production Safety
Always check `config('app.debug')` before including debug information in responses.

### Route Naming
The issue occurs because the view `resources/views/admin/change-password.blade.php` references `route('admin.update-password')` but the routes file defines the POST route as `admin.change-password`. The fix is to either:
1. Update the route definition to use `->name('update-password')` for the POST route, OR
2. Update the view to use `route('admin.change-password')`

Option 1 is preferred as it provides clearer semantic distinction between the GET (show form) and POST (process form) routes.
