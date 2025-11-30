<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CalAPI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for CalAPI integration
    |
    */

    'api_key' => env('CALAPI_KEY', ''),

    'base_url' => env('CALAPI_BASE_URL', 'https://api.calapi.io'),

    'account_id' => env('CALAPI_ACCOUNT_ID'),

    'calendar_id' => env('CALAPI_CALENDAR_ID', 'primary'),

    'timezone' => env('CALAPI_TIMEZONE', 'Asia/Manila'),

    'api_version' => env('CALAPI_VERSION', '2024-08-13'),

    /*
    |--------------------------------------------------------------------------
    | Booking Settings
    |--------------------------------------------------------------------------
    */

    'default_duration' => 60, // minutes

    'business_hours' => [
        'start' => '08:00',
        'end' => '18:00',
    ],

    'slot_interval' => 30, // minutes

    'max_bookings_per_slot' => 5,
];
