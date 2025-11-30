<?php

/**
 * Quick View Test Script
 * Tests all Blade views for compilation errors
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$viewPaths = [
    // User Views
    'user.login',
    'user.register',
    'user.forgot-password',
    'user.reset-password',
    'user.dashboard',
    'user.profile',
    'user.booking',
    'user.history',
    'user.status',
    'user.route-to-admin',
    'user.receipt',
    
    // Admin Views
    'admin.login',
    'admin.dashboard',
    'admin.profile',
    'admin.create-admin',
    'admin.route-to-user',
    'admin.bookings.index',
    
    // Landing
    'landingPage',
];

$errors = [];
$success = [];

foreach ($viewPaths as $view) {
    try {
        if (view()->exists($view)) {
            // Try to compile the view
            view($view)->render();
            $success[] = $view;
            echo "✅ {$view}\n";
        } else {
            $errors[] = "{$view} - View does not exist";
            echo "❌ {$view} - Does not exist\n";
        }
    } catch (\Exception $e) {
        $errors[] = "{$view} - " . $e->getMessage();
        echo "❌ {$view} - Error: " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "========================================\n";
echo "Summary:\n";
echo "✅ Success: " . count($success) . "\n";
echo "❌ Errors: " . count($errors) . "\n";
echo "========================================\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "  - {$error}\n";
    }
    exit(1);
}

echo "\n✅ All views compiled successfully!\n";
exit(0);
