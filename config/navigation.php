<?php

return [
    'admin' => [
        'title' => 'Admin Panel',
        'color' => 'blue',
        'links' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ['label' => 'Profile', 'route' => 'admin.profile'],
            ['label' => 'Create Admin', 'route' => 'admin.create-admin'],
        ],
    ],
    'user' => [
        'title' => 'User Panel',
        'color' => 'green',
        'links' => [
            ['label' => 'Dashboard', 'route' => 'user.dashboard'],
            ['label' => 'Profile', 'route' => 'user.profile'],
        ],
    ],
];
