# Multi-Step Form Manual Testing Guide

This guide provides step-by-step instructions for manually testing the multi-step forms after the bug fixes have been applied.

## Prerequisites

1. Ensure the Laravel application is running (`php artisan serve`)
2. Ensure the database is set up and migrations are run
3. Have access to a phone number that can receive SMS (for OTP testing)
4. Open browser developer tools (F12) to monitor console logs and network requests

## Test 1: User Registration Form Flow

### Objective
Verify the complete user registration flow works correctly from contact information through OTP verification to final registration.

**Validates Requirements:** 2.1, 2.2, 2.3, 2.4, 2.5

### Steps

1. **Navigate to Registration Page**
   - Go to `/user/register`
   - ✅ Verify: Page loads without errors
   - ✅ Verify: Step 1 is visible, Steps 2 and 3 are hidden
   - ✅ Verify: Progress indicator shows Step 1 as active (green/blue)

2. **Test Step 1: Contact Information**
   
   **Test 2.1a: Empty Form Submission**
   - Leave email and phone fields empty
   - Click "Send OTP" button
   - ✅ Verify: Error message appears: "Please fill in all fields"
   - ✅ Verify: Form does not advance to Step 2
   - **Validates: Requirement 2.4 - Error message display**

   **Test 2.1b: Valid Contact Submission**
   - Enter valid email: `test@example.com`
   - Enter valid phone: `09123456789`
   - Click "Send OTP" button
   - ✅ Verify: Button text changes to "Sending..." and button is disabled
   - ✅ Verify: After response, button re-enables
   - ✅ Verify: On success, form advances to Step 2
   - ✅ Verify: Progress indicator shows Step 2 as active
   - ✅ Verify: OTP is sent to the phone number
   - **Validates: Requirements 2.1, 2.3, 2.5 - OTP sending, navigation, button states**

   **Test 2.1c: Network Error Handling**
   - Disconnect internet or stop the server
   - Try to send OTP
   - ✅ Verify: Error message appears: "Network error. Please check your connection and try again."
   - ✅ Verify: Button re-enables after error
   - **Validates: Requirements 2.4, 2.5 - Error handling, button states**

3. **Test Step 2: OTP Verification**
   
   **Test 2.2a: Back Button**
   - Click "Back" button
   - ✅ Verify: Form returns to Step 1
   - ✅ Verify: Progress indicator updates correctly
   - ✅ Verify: Previously entered email and phone are still filled
   - **Validates: Requirement 2.3 - Step navigation**

   **Test 2.2b: Invalid OTP**
   - Navigate back to Step 2 (send OTP again if needed)
   - Enter invalid OTP: `000000`
   - Click "Verify OTP" button
   - ✅ Verify: Button text changes to "Verifying..." and button is disabled
   - ✅ Verify: After response, button re-enables
   - ✅ Verify: Error message appears: "Invalid OTP. Please try again."
   - ✅ Verify: Form does not advance to Step 3
   - **Validates: Requirements 2.2, 2.4, 2.5 - OTP verification, error display, button states**

   **Test 2.2c: Valid OTP**
   - Enter the correct OTP received via SMS
   - Click "Verify OTP" button
   - ✅ Verify: Button text changes to "Verifying..." and button is disabled
   - ✅ Verify: After response, button re-enables
   - ✅ Verify: Form advances to Step 3
   - ✅ Verify: Progress indicator shows Step 3 as active
   - **Validates: Requirements 2.2, 2.3, 2.5 - OTP verification, navigation, button states**

4. **Test Step 3: User Details**
   
   **Test 2.3a: Back Button**
   - Click "Back" button
   - ✅ Verify: Form returns to Step 2
   - ✅ Verify: Progress indicator updates correctly
   - **Validates: Requirement 2.3 - Step navigation**

   **Test 2.3b: Complete Registration**
   - Navigate back to Step 3
   - Fill in all required fields:
     - Username
     - First Name
     - Last Name
     - Address
     - Password (min 8 characters)
     - Confirm Password
   - Click "Create Account" button
   - ✅ Verify: Registration completes successfully
   - ✅ Verify: User is redirected appropriately
   - **Validates: Requirement 2.3 - Complete form flow**

## Test 2: Admin Create Admin Form Flow

### Objective
Verify the complete admin creation flow works correctly from contact information through OTP verification to final admin creation.

**Validates Requirements:** 1.5, 2.1, 2.2, 2.3, 2.4, 2.5

### Prerequisites
- Log in as an admin user first

### Steps

1. **Navigate to Create Admin Page**
   - Go to `/admin/create-admin`
   - ✅ Verify: Page loads without errors
   - ✅ Verify: Step 1 is visible, Steps 2 and 3 are hidden
   - ✅ Verify: Progress indicator shows Step 1 as active (blue)

2. **Test Step 1: Contact Information**
   
   **Test 2.1a: Empty Form Submission**
   - Leave email and phone fields empty
   - Click "Send OTP" button
   - ✅ Verify: Error message appears: "Please fill in all fields"
   - ✅ Verify: Form does not advance to Step 2
   - **Validates: Requirement 2.4 - Error message display**

   **Test 2.1b: Valid Contact Submission**
   - Enter valid email: `newadmin@example.com`
   - Enter valid phone: `09123456789`
   - Click "Send OTP" button
   - ✅ Verify: Button text changes to "Sending..." and button is disabled
   - ✅ Verify: After response, button re-enables
   - ✅ Verify: On success, form advances to Step 2
   - ✅ Verify: Progress indicator shows Step 2 as active
   - ✅ Verify: OTP is sent to the phone number
   - **Validates: Requirements 1.5, 2.1, 2.3, 2.5 - Admin OTP sending, navigation, button states**

3. **Test Step 2: OTP Verification**
   
   **Test 2.2a: Back Button**
   - Click "Back" button
   - ✅ Verify: Form returns to Step 1
   - ✅ Verify: Progress indicator updates correctly
   - **Validates: Requirement 2.3 - Step navigation**

   **Test 2.2b: Invalid OTP**
   - Navigate back to Step 2
   - Enter invalid OTP: `000000`
   - Click "Verify OTP" button
   - ✅ Verify: Button text changes to "Verifying..." and button is disabled
   - ✅ Verify: After response, button re-enables
   - ✅ Verify: Error message appears
   - ✅ Verify: Form does not advance to Step 3
   - **Validates: Requirements 2.2, 2.4, 2.5 - OTP verification, error display, button states**

   **Test 2.2c: Valid OTP**
   - Enter the correct OTP received via SMS
   - Click "Verify OTP" button
   - ✅ Verify: Button text changes to "Verifying..." and button is disabled
   - ✅ Verify: After response, button re-enables
   - ✅ Verify: Form advances to Step 3
   - ✅ Verify: Progress indicator shows Step 3 as active
   - **Validates: Requirements 2.2, 2.3, 2.5 - OTP verification, navigation, button states**

4. **Test Step 3: Admin Details**
   
   **Test 2.3a: Back Button**
   - Click "Back" button
   - ✅ Verify: Form returns to Step 2
   - ✅ Verify: Progress indicator updates correctly
   - **Validates: Requirement 2.3 - Step navigation**

   **Test 2.3b: Complete Admin Creation**
   - Navigate back to Step 3
   - Fill in all required fields:
     - Admin Username
     - First Name
     - Last Name
     - Address
     - Password (min 8 characters)
     - Confirm Password
   - Click "Create Admin" button
   - ✅ Verify: Admin creation completes successfully
   - ✅ Verify: Appropriate success message or redirect occurs
   - **Validates: Requirement 2.3 - Complete form flow**

## Test 3: Browser Console Verification

### Objective
Verify that JavaScript is working correctly without errors.

### Steps

1. Open browser developer tools (F12)
2. Go to Console tab
3. Navigate through both forms (user registration and admin creation)
4. ✅ Verify: No JavaScript errors appear in console
5. ✅ Verify: Console logs show expected behavior (if any debug logs are present)
6. **Validates: Requirements 1.1, 1.4 - Correct parent method calls**

## Test 4: Network Request Verification

### Objective
Verify that API calls are being made correctly.

### Steps

1. Open browser developer tools (F12)
2. Go to Network tab
3. Filter by "Fetch/XHR"
4. **For User Registration:**
   - Send OTP from Step 1
   - ✅ Verify: POST request to `/user/send-registration-otp`
   - ✅ Verify: Request includes email and phone in body
   - ✅ Verify: Response is received
   - Verify OTP from Step 2
   - ✅ Verify: POST request to `/user/verify-otp`
   - ✅ Verify: Request includes phone and otp in body
   - ✅ Verify: Response is received
5. **For Admin Creation:**
   - Send OTP from Step 1
   - ✅ Verify: POST request to `/admin/send-admin-otp`
   - ✅ Verify: Request includes email and phone in body
   - ✅ Verify: Response is received
   - Verify OTP from Step 2
   - ✅ Verify: POST request to `/admin/verify-admin-otp`
   - ✅ Verify: Request includes phone and otp in body
   - ✅ Verify: Response is received
6. **Validates: Requirements 1.2, 1.3 - Parent API methods work correctly**

## Test Results Summary

After completing all tests, fill in this summary:

### User Registration Form
- [ ] Step navigation works correctly
- [ ] OTP sending works correctly
- [ ] OTP verification works correctly
- [ ] Error messages display correctly
- [ ] Button states change appropriately
- [ ] Complete registration flow works end-to-end

### Admin Create Admin Form
- [ ] Step navigation works correctly
- [ ] OTP sending works correctly
- [ ] OTP verification works correctly
- [ ] Error messages display correctly
- [ ] Button states change appropriately
- [ ] Complete admin creation flow works end-to-end

### Overall
- [ ] No JavaScript errors in console
- [ ] All API requests are made correctly
- [ ] All requirements validated (1.1, 1.2, 1.3, 1.4, 1.5, 2.1, 2.2, 2.3, 2.4, 2.5)

## Test 5: Password Strength and Match Validation (Both Forms)

### Objective
Verify that password strength indicator and password match validation work correctly on both user registration and admin creation forms.

**Validates Requirements:** User experience and form validation consistency

### Steps - User Registration Form

1. **Navigate to User Registration Page**
   - Complete Steps 1 and 2 to reach Step 3 (User Details)

2. **Test Password Strength Indicator**
   
   **Test 5.1a: Weak Password**
   - Enter a weak password: `pass`
   - ✅ Verify: Strength bar appears red
   - ✅ Verify: Text shows "Weak password" in red
   - ✅ Verify: Bar width is less than 40%

   **Test 5.1b: Medium Password**
   - Enter a medium password: `password123`
   - ✅ Verify: Strength bar appears yellow
   - ✅ Verify: Text shows "Medium password" in yellow
   - ✅ Verify: Bar width is between 40-70%

   **Test 5.1c: Strong Password**
   - Enter a strong password: `MyP@ssw0rd123!`
   - ✅ Verify: Strength bar appears green
   - ✅ Verify: Text shows "Strong password" in green
   - ✅ Verify: Bar width is greater than 70%

3. **Test Password Match Validation**
   
   **Test 5.2a: Matching Passwords**
   - Enter password: `MyP@ssw0rd123!`
   - Enter same password in confirm field
   - ✅ Verify: Text shows "✓ Passwords match" in green

   **Test 5.2b: Non-Matching Passwords**
   - Enter password: `MyP@ssw0rd123!`
   - Enter different password in confirm field: `DifferentPass123!`
   - ✅ Verify: Text shows "✗ Passwords do not match" in red

### Steps - Admin Create Admin Form

1. **Navigate to Admin Create Admin Page**
   - Log in as admin
   - Complete Steps 1 and 2 to reach Step 3 (Admin Details)

2. **Test Password Strength Indicator**
   
   **Test 5.3a: Weak Password**
   - Enter a weak password: `pass`
   - ✅ Verify: Strength bar appears red
   - ✅ Verify: Text shows "Weak password" in red
   - ✅ Verify: Bar width is less than 40%

   **Test 5.3b: Medium Password**
   - Enter a medium password: `password123`
   - ✅ Verify: Strength bar appears yellow
   - ✅ Verify: Text shows "Medium password" in yellow
   - ✅ Verify: Bar width is between 40-70%

   **Test 5.3c: Strong Password**
   - Enter a strong password: `MyP@ssw0rd123!`
   - ✅ Verify: Strength bar appears green
   - ✅ Verify: Text shows "Strong password" in green
   - ✅ Verify: Bar width is greater than 70%

3. **Test Password Match Validation**
   
   **Test 5.4a: Matching Passwords**
   - Enter password: `MyP@ssw0rd123!`
   - Enter same password in confirm field
   - ✅ Verify: Text shows "✓ Passwords match" in green

   **Test 5.4b: Non-Matching Passwords**
   - Enter password: `MyP@ssw0rd123!`
   - Enter different password in confirm field: `DifferentPass123!`
   - ✅ Verify: Text shows "✗ Passwords do not match" in red

4. **Test Password Validation HTML File**
   - Open `tests/manual-test-password-validation.html` in a browser
   - Follow the test cases listed on the page
   - ✅ Verify: All password strength levels work correctly
   - ✅ Verify: Password match/mismatch detection works correctly

## Known Issues or Notes

### Fixed Issues (Latest Update)
1. ✅ **Fixed:** User registration form now exposes `sendOTP()` and `verifyOTP()` globally
2. ✅ **Fixed:** Password strength indicator now works correctly on both forms
3. ✅ **Fixed:** Password match validation now works correctly on both forms
4. ✅ **Fixed:** Hidden fields are now populated with verified data before form submission
5. ✅ **Fixed:** Form no longer returns to step 1 after step 3
6. ✅ **Fixed:** Password validation moved to shared module (`password-validator.js`) for UI/UX consistency
7. ✅ **Fixed:** Admin create form now has password strength indicator (consistent with user registration)

Document any NEW issues found during testing:

---

## Automated Test Results

The following automated tests have been created and are passing:

```
✓ user registration form renders with all steps
✓ admin create form renders with all steps
✓ user registration send otp endpoint
✓ user registration send otp validation error
✓ user registration verify otp endpoint
✓ admin send otp endpoint
✓ admin send otp validation error
✓ admin verify otp endpoint
✓ user registration loads javascript
✓ admin create loads javascript
✓ user registration has required buttons
✓ admin create has required buttons

Tests: 12 passed (54 assertions)
```

These automated tests verify the core functionality programmatically and complement the manual testing outlined above.
