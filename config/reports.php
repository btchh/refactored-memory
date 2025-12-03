<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Report Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for various reports in the system.
    |
    */

    'company' => [
        'name' => env('REPORT_COMPANY_NAME', env('APP_NAME', 'Laundry Management System')),
        'address' => env('REPORT_COMPANY_ADDRESS', ''),
        'phone' => env('REPORT_COMPANY_PHONE', ''),
        'email' => env('REPORT_COMPANY_EMAIL', ''),
        'logo' => env('REPORT_COMPANY_LOGO', null), // Path to logo image
    ],

    'revenue' => [
        'title' => 'Revenue Report',
        'subtitle' => 'Financial Summary',
        'currency' => '₱',
        'currency_name' => 'PHP',
        'decimal_places' => 2,
        'show_logo' => true,
        'show_company_info' => true,
        'show_generated_by' => true,
        'show_report_id' => true,
        'date_format' => 'F d, Y',
        'time_format' => 'h:i A',
        'footer_text' => 'This is a computer-generated report. No signature required.',
    ],

    'print' => [
        'page_size' => 'A4',
        'orientation' => 'portrait',
        'margin_top' => '1.5cm',
        'margin_bottom' => '1.5cm',
        'margin_left' => '1.5cm',
        'margin_right' => '1.5cm',
    ],
];
