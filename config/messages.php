<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Message Templates
    |--------------------------------------------------------------------------
    |
    | These are the predefined SMS message templates for the WashHour
    | Laundry Management System. Variables are marked with :variable_name
    | and will be replaced with actual values when sending messages.
    |
    */

    'otp' => [
        'verification' => 'Your verification code is :otp. Valid for 5 minutes. Do not share this code with anyone.',
        'password_reset' => 'Your password reset code is :otp. Valid for 5 minutes. If you did not request this, please ignore this message.',
    ],

    'booking' => [
        'confirmed' => 'Hi :customer_name! Your :service_description booking #:booking_id for :schedule has been confirmed. Thank you for choosing WashHour!',
        'laundry_completed' => 'Hi :customer_name! Your laundry for booking #:booking_id is now ready for :action. Thank you for your patience!',
        'out_for_delivery' => 'Hi :customer_name! Your laundry for booking #:booking_id is out for delivery. Estimated arrival: :eta. Please ensure someone is available to receive it.',
        'delivered' => 'Hi :customer_name! Your laundry for booking #:booking_id has been delivered. Thank you for choosing WashHour!',
        'cancelled' => 'Hi :customer_name! Your booking #:booking_id scheduled for :schedule has been cancelled. Reason: :reason. For concerns, please contact us.',
        'cancelled_by_admin' => 'Hi :customer_name! We apologize, but your booking #:booking_id for :schedule has been cancelled by our admin. Reason: :reason. Please contact us for rebooking.',
        'rescheduled' => 'Hi :customer_name! Your booking #:booking_id has been rescheduled to :schedule. Thank you for your understanding!',
    ],

    'reminder' => [
        'pickup_tomorrow' => 'Reminder! Your laundry booking #:booking_id is scheduled for tomorrow, :schedule. We look forward to serving you!',
        'delivery_tomorrow' => 'Reminder! Your pickup and delivery service #:booking_id is scheduled for tomorrow, :schedule. Please prepare your laundry items.',
    ],

    'admin' => [
        'booking_cancelled_by_customer' => 'ALERT: Customer :customer_name has cancelled booking #:booking_id scheduled for :schedule. Reason: :reason. Please review and update your schedule.',
        'new_booking' => 'New booking received! Booking #:booking_id from :customer_name for :schedule. Service: :service_description. Please prepare accordingly.',
    ],

    'general' => [
        'welcome' => 'Welcome to WashHour Laundry Shop! Your registration is successful. You can now book our services online. For assistance, contact us at :contact_number.',
    ],

];
