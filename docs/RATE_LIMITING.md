# 🛡️ Progressive Rate Limiting System

## Overview

The system implements **progressive rate limiting** that increases lockout duration with each violation. This protects against brute force attacks, spam, and abuse while allowing legitimate users to continue after a timeout.

---

## How It Works

### Progressive Lockout Strategy

Each time a user exceeds the rate limit, the lockout time increases by **5 minutes**:

```
1st violation → 5 minutes lockout
2nd violation → 10 minutes lockout
3rd violation → 15 minutes lockout
4th violation → 20 minutes lockout
...and so on
```

### Violation Tracking

- Violations are tracked per IP address (for guests) or user ID (for authenticated users)
- Violation count resets after 24 hours of no violations
- Successful requests reset the attempt counter (but not violation history)

---

## Protected Endpoints

### 🔐 Login Attempts
**Limit:** 5 attempts  
**Applied to:**
- User login (`POST /user/login`)
- Admin login (`POST /admin/login`)
- Password reset submission

**Example:**
```
Attempt 1-5: Allowed
Attempt 6: Locked for 5 minutes
After 5 min, Attempt 6-10: Allowed
Attempt 11: Locked for 10 minutes
```

### 📝 Registration
**Limit:** 3 attempts  
**Applied to:**
- User registration (`POST /user/register`)

### 📱 OTP Requests
**Limit:** 3 attempts  
**Applied to:**
- Send registration OTP (`POST /user/send-registration-otp`)
- Verify registration OTP (`POST /user/verify-otp`)
- Send password reset OTP (`POST /user/send-password-reset-otp`)
- Verify password reset OTP (`POST /user/verify-password-reset-otp`)
- Admin password reset OTP (`POST /admin/send-password-reset`)

### 📅 Booking Requests
**Limit:** 10 attempts  
**Applied to:**
- User booking submission (`POST /user/booking`)
- Admin booking creation (`POST /admin/bookings`)

---

## Response Format

When rate limit is exceeded:

```json
{
  "message": "Too many attempts. Account locked for 5 minute(s).",
  "retry_after": 300,
  "lockout_minutes": 5
}
```

HTTP Status: **429 Too Many Requests**

---

## Technical Implementation

### Middleware: `ProgressiveRateLimiter`

**Location:** `app/Http/Middleware/ProgressiveRateLimiter.php`

**Key Features:**
- Uses Laravel Cache for tracking attempts and violations
- Separate tracking for different action types (login, register, otp, booking)
- IP-based tracking for guests, user ID-based for authenticated users
- Automatic cleanup after 24 hours

### Cache Keys

```
rate_limit:{action}:{ip}                    // For guests
rate_limit:{action}:{guard}:{user_id}       // For authenticated users
rate_limit:{action}:{identifier}:attempts   // Current attempt count
rate_limit:{action}:{identifier}:lockout    // Lockout expiration timestamp
rate_limit:{action}:{identifier}:violations // Total violation count
```

### Configuration

Edit `app/Http/Middleware/ProgressiveRateLimiter.php` to adjust limits:

```php
protected function getMaxAttempts(string $limiterName): int
{
    return match($limiterName) {
        'login' => 5,           // 5 login attempts
        'register' => 3,        // 3 registration attempts
        'otp' => 3,             // 3 OTP requests
        'booking' => 10,        // 10 booking attempts
        default => 5,
    };
}
```

---

## Usage in Routes

Apply the middleware to any route:

```php
Route::post('login', [LoginController::class, 'login'])
    ->middleware('rate.limit.progressive:login');

Route::post('send-otp', [OtpController::class, 'send'])
    ->middleware('rate.limit.progressive:otp');

Route::post('booking', [BookingController::class, 'store'])
    ->middleware('rate.limit.progressive:booking');
```

---

## Security Benefits

### 1. **Brute Force Protection**
- Prevents password guessing attacks
- Exponentially increases difficulty with each attempt

### 2. **Spam Prevention**
- Limits OTP request spam
- Prevents booking system abuse

### 3. **Resource Protection**
- Reduces server load from malicious requests
- Protects SMS gateway from abuse

### 4. **User-Friendly**
- Legitimate users can retry after timeout
- Clear error messages with retry time
- Automatic reset on successful actions

---

## Monitoring & Debugging

### Check Current Lockouts

```php
use Illuminate\Support\Facades\Cache;

// Check if user is locked out
$key = 'rate_limit:login:user:123:lockout';
if (Cache::has($key)) {
    $expiresAt = Cache::get($key);
    $remaining = $expiresAt - now()->timestamp;
    echo "Locked for {$remaining} seconds";
}
```

### View Violation Count

```php
$key = 'rate_limit:login:user:123:violations';
$violations = Cache::get($key, 0);
echo "Total violations: {$violations}";
```

### Clear User Lockout (Admin Action)

```php
// Clear all rate limit data for a user
Cache::forget('rate_limit:login:user:123:attempts');
Cache::forget('rate_limit:login:user:123:lockout');
Cache::forget('rate_limit:login:user:123:violations');
```

---

## Best Practices

### For Developers

1. **Apply to sensitive endpoints only** - Don't rate limit read-only operations
2. **Use appropriate limits** - Balance security with user experience
3. **Log violations** - Monitor for attack patterns
4. **Provide clear feedback** - Tell users when they can retry

### For Administrators

1. **Monitor violation patterns** - High violations may indicate attacks
2. **Adjust limits if needed** - Based on legitimate user behavior
3. **Clear cache if needed** - For false positives or testing
4. **Review logs regularly** - Identify potential security threats

---

## Troubleshooting

### Issue: Legitimate users getting locked out

**Solution:** Increase the max attempts for that action type

```php
'login' => 10,  // Increased from 5 to 10
```

### Issue: Lockout time too long

**Solution:** Reduce the increment multiplier (currently 5 minutes)

```php
$lockoutMinutes = $violations * 3;  // Changed from 5 to 3
```

### Issue: Need to clear all rate limits

**Solution:** Clear the cache

```bash
php artisan cache:clear
```

---

## Version History

**v1.0.0** - December 4, 2025
- Initial implementation
- Progressive lockout (5-minute increments)
- Applied to login, register, OTP, and booking endpoints
- 24-hour violation tracking

---

<p align="center">
  <strong>WashHour Security System</strong><br>
  Progressive Rate Limiting for Enhanced Protection
</p>
