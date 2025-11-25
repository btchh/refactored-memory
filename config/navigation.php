<?php

return [
    'admin' => [
        'title' => 'Admin Panel',
        'color' => 'blue',
        'links' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Profile', 'route' => 'admin.profile'],
            ['label' => 'Bookings', 'route' => 'admin.bookings'],
            ['label' => 'Route to User', 'route' => 'admin.route-to-user'],
            ['label' => 'Create Admin', 'route' => 'admin.create-admin'],
        ],
    ],
    'user' => [
        'title' => 'User Panel',
        'color' => 'green',
        'links' => [
            ['label' => 'Dashboard', 'route' => 'user.dashboard'],
            ['label' => 'Profile', 'route' => 'user.profile'],
            ['label' => 'Bookings', 'route' => 'user.bookings'],
            ['label' => 'Track Admin', 'route' => 'user.track-admin'],
        ],
    ],
];
