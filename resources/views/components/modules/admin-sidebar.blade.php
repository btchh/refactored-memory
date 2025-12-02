@php
    $isActive = fn ($name) => request()->routeIs($name);
@endphp

<aside class="sidebar h-full">
    <nav class="flex flex-col gap-1">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ $isActive('admin.dashboard') ? 'active' : '' }}"
           aria-label="Dashboard">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        <!-- Booking Management -->
        <a href="{{ route('admin.bookings.manage') }}"
           class="sidebar-link {{ $isActive('admin.bookings.manage') ? 'active' : '' }}"
           aria-label="Booking Management">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span class="text-sm font-medium">Bookings</span>
        </a>
        
        <!-- Create Booking -->
        <a href="{{ route('admin.bookings.index') }}"
           class="sidebar-link {{ $isActive('admin.bookings.index') ? 'active' : '' }}"
           aria-label="Create Booking">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-sm font-medium">Create Booking</span>
        </a>

        <!-- Pricing -->
        <a href="{{ route('admin.pricing.index') }}"
           class="sidebar-link {{ $isActive('admin.pricing.index') ? 'active' : '' }}"
           aria-label="Pricing">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">Pricing</span>
        </a>

        <!-- Analytics -->
        <a href="{{ route('admin.analytics.index') }}"
           class="sidebar-link {{ $isActive('admin.analytics.index') ? 'active' : '' }}"
           aria-label="Analytics">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span class="text-sm font-medium">Analytics</span>
        </a>

        <!-- User Management -->
        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link {{ $isActive('admin.users.*') ? 'active' : '' }}"
           aria-label="User Management">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span class="text-sm font-medium">Users</span>
        </a>

        <!-- Revenue Report -->
        <a href="{{ route('admin.revenue.index') }}"
           class="sidebar-link {{ $isActive('admin.revenue.*') ? 'active' : '' }}"
           aria-label="Revenue Report">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <span class="text-sm font-medium">Revenue</span>
        </a>

        <!-- Messages -->
        <a href="{{ route('admin.messages.index') }}"
           class="sidebar-link {{ $isActive('admin.messages.*') ? 'active' : '' }}"
           aria-label="Messages">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <span class="text-sm font-medium">Messages</span>
        </a>

        <!-- Route to User -->
        <a href="{{ route('admin.route-to-user') }}"
           class="sidebar-link {{ $isActive('admin.route-to-user') ? 'active' : '' }}"
           aria-label="Route to User">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
            <span class="text-sm font-medium">Delivery Route</span>
        </a>

        <!-- Audit Log -->
        <a href="{{ route('admin.audit') }}"
           class="sidebar-link {{ $isActive('admin.audit') ? 'active' : '' }}"
           aria-label="Audit Log">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="text-sm font-medium">Audit Log</span>
        </a>

        <!-- Create Admin -->
        <a href="{{ route('admin.create-admin.show') }}"
           class="sidebar-link {{ $isActive('admin.create-admin.*') ? 'active' : '' }}"
           aria-label="Create Admin">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            <span class="text-sm font-medium">Create Admin</span>
        </a>
    </nav>
</aside>
