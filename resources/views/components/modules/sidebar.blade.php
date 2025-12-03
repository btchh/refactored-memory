@php
    $isActive = fn ($name) => request()->routeIs($name);
@endphp

<aside class="sidebar h-full">
    <nav class="flex flex-col gap-1">
        <!-- Dashboard -->
        <a href="{{ route('user.dashboard') }}"
           class="sidebar-link {{ $isActive('user.dashboard') ? 'active' : '' }}"
           aria-label="Dashboard">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        <!-- Book Laundry -->
        <a href="{{ route('user.booking') }}"
           class="sidebar-link {{ $isActive('user.booking') ? 'active' : '' }}"
           aria-label="Book Laundry">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-sm font-medium">Book Laundry</span>
        </a>

        <!-- Laundry Status -->
        <a href="{{ route('user.status') }}"
           class="sidebar-link {{ $isActive('user.status') ? 'active' : '' }}"
           aria-label="Laundry Status">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span class="text-sm font-medium">Laundry Status</span>
        </a>

        <!-- Route to Shop -->
        <a href="{{ route('user.route-to-admin') }}"
           class="sidebar-link {{ $isActive('user.route-to-admin') ? 'active' : '' }}"
           aria-label="Route to Shop">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="text-sm font-medium">Location</span>
        </a>

        <!-- History -->
        <a href="{{ route('user.history') }}"
           class="sidebar-link {{ $isActive('user.history') ? 'active' : '' }}"
           aria-label="History Booking">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">History</span>
        </a>

        <!-- Messages -->
        <a href="{{ route('user.messages.index') }}"
           class="sidebar-link {{ $isActive('user.messages.*') ? 'active' : '' }}"
           aria-label="Messages">
            <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <span class="text-sm font-medium">Messages</span>
        </a>
    </nav>
</aside>

