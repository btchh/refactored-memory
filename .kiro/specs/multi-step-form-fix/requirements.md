# Requirements Document

## Introduction

This document specifies the requirements for fixing the multi-step form functionality that broke after recent updates. The multi-step forms used in user registration and admin creation are currently non-functional due to method naming conflicts and incorrect API method calls.

## Glossary

- **Multi-Step Form**: A form divided into multiple sequential steps with progress indicators
- **OTP**: One-Time Password used for verification
- **Parent Class Method**: A method defined in the MultiStepForm base class
- **Child Class**: Classes that extend MultiStepForm (UserRegistration, AdminCreateAdmin)

## Requirements

### Requirement 1

**User Story:** As a developer, I want the multi-step form classes to correctly call parent class methods, so that OTP sending and verification work properly.

#### Acceptance Criteria

1. WHEN a child class method calls a parent class API method THEN the system SHALL invoke the correct parent class method without naming conflicts
2. WHEN the sendOTP parent method is called THEN the system SHALL make the API request and return the response
3. WHEN the verifyOTP parent method is called THEN the system SHALL make the API request and return the response
4. WHEN method names conflict between parent and child classes THEN the system SHALL use distinct naming to avoid recursion
5. WHEN the admin create form sends OTP THEN the system SHALL successfully call the parent sendOTP method

### Requirement 2

**User Story:** As a user, I want to complete multi-step registration forms, so that I can register or create admin accounts.

#### Acceptance Criteria

1. WHEN I submit contact information THEN the system SHALL send an OTP to my phone
2. WHEN I submit a valid OTP THEN the system SHALL verify it and proceed to the next step
3. WHEN I navigate between form steps THEN the system SHALL show the correct step content
4. WHEN an API call fails THEN the system SHALL display an appropriate error message
5. WHEN form buttons are clicked THEN the system SHALL disable them during processing and re-enable after completion
