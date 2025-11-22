# Design Document

## Overview

The multi-step form system consists of a base `MultiStepForm` class that provides common functionality for OTP-based multi-step forms, and two child classes (`UserRegistration` and `AdminCreateAdmin`) that implement specific form behaviors. The current issue is a method naming conflict where child class methods have the same names as parent class methods they need to call, causing recursion issues.

## Architecture

The system uses a class-based inheritance pattern:
- `MultiStepForm` (base class) - Provides core functionality for step navigation, OTP sending/verification, and error handling
- `UserRegistration` (child class) - Implements user registration flow
- `AdminCreateAdmin` (child class) - Implements admin creation flow

## Components and Interfaces

### MultiStepForm (Base Class)

**Properties:**
- `currentStep`: Current step number
- `totalSteps`: Total number of steps in the form
- `verifiedData`: Object storing verified data from previous steps
- `config`: Configuration object with styling classes

**Methods:**
- `goToStep(step)`: Navigate to a specific step
- `updateProgressIndicators(currentStep)`: Update visual progress indicators
- `showError(elementId, message)`: Display error message
- `hideError(elementId)`: Hide error message
- `sendOTP(url, data, button, errorElementId)`: Send OTP request to API
- `verifyOTP(url, data, button, errorElementId)`: Verify OTP with API
- `reset()`: Reset form to initial state

### UserRegistration (Child Class)

**Methods:**
- `init()`: Initialize event listeners
- `setupEventListeners()`: Attach form submission handlers
- `handleContactSubmit()`: Handle contact form submission (calls parent `sendOTP`)
- `handleOtpSubmit()`: Handle OTP form submission (calls parent `verifyOTP`)

### AdminCreateAdmin (Child Class)

**Methods:**
- `init()`: Initialize event listeners
- `setupEventListeners()`: Expose methods globally for onclick handlers
- `handleSendOTP()`: Handle send OTP button click (calls parent `sendOTP`)
- `handleVerifyOTP()`: Handle verify OTP button click (calls parent `verifyOTP`)

## Data Models

### Verified Data Object
```javascript
{
    email: string,    // Verified email address
    phone: string,    // Verified phone number
    otp: string       // Verified OTP code
}
```

### API Response Format
```javascript
{
    success: boolean,
    data: object,
    status: number
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Parent method invocation correctness
*For any* child class method that needs to call a parent class API method, calling the parent method should execute the parent's implementation without recursion or naming conflicts.
**Validates: Requirements 1.1, 1.4**

### Property 2: OTP sending consistency
*For any* valid email and phone combination, calling the sendOTP parent method should make exactly one API request and return a response object with success, data, and status fields.
**Validates: Requirements 1.2, 2.1**

### Property 3: OTP verification consistency
*For any* valid phone and OTP combination, calling the verifyOTP parent method should make exactly one API request and return a response object with success, data, and status fields.
**Validates: Requirements 1.3, 2.2**

### Property 4: Button state management
*For any* API call (sendOTP or verifyOTP), the button should be disabled during the request and re-enabled after completion, regardless of success or failure.
**Validates: Requirements 2.5**

### Property 5: Error display consistency
*For any* failed API call, an error message should be displayed in the specified error element.
**Validates: Requirements 2.4**

## Error Handling

1. **Network Errors**: Catch fetch errors and display user-friendly messages
2. **API Errors**: Parse response and display server-provided error messages
3. **Validation Errors**: Validate input before making API calls
4. **Button State**: Always re-enable buttons after API calls complete (success or failure)
5. **Error Display**: Show errors in designated error elements with appropriate styling

## Testing Strategy

### Unit Tests
- Test that parent methods are callable from child classes
- Test button state changes during API calls
- Test error message display/hide functionality
- Test step navigation logic

### Property-Based Tests
We will use a JavaScript property-based testing library (fast-check) to verify the correctness properties.

Each property-based test will:
- Run a minimum of 100 iterations
- Be tagged with a comment referencing the design document property
- Use the format: `**Feature: multi-step-form-fix, Property {number}: {property_text}**`

Property tests will verify:
1. Parent method calls work correctly across all valid input combinations
2. API methods return consistent response formats
3. Button states are managed correctly for all API call outcomes
4. Error messages are displayed for all failure scenarios

## Implementation Approach

The fix involves renaming the parent class methods to avoid naming conflicts:

1. **Rename parent methods**: Change `sendOTP` and `verifyOTP` in the base class to `_sendOTPRequest` and `_verifyOTPRequest` (using underscore prefix to indicate internal/parent methods)

2. **Update child class calls**: Update all child class methods to call the renamed parent methods

3. **Maintain public API**: The child classes can still expose `sendOTP` and `verifyOTP` as public methods if needed, but they will call the renamed parent methods internally

This approach ensures:
- No naming conflicts between parent and child methods
- Clear distinction between internal API methods and public handlers
- Backward compatibility with existing code that calls these methods
