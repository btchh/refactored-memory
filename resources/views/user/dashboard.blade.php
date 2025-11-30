<x-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="space-y-8">
        <!-- Welcome Banner -->
        <div class="card p-8">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-full bg-primary-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Welcome Back!</h1>
                    <p class="text-lg text-gray-600">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
                </div>
            </div>
            <p class="text-base text-gray-600">Ready to make your laundry day easier? Let's get started!</p>
        </div>

        <!-- Quick Stats -->
        <div class="card-grid card-grid-3">
            <div class="stat-card hover:shadow-md">
                <div class="stat-icon-container">
                    <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <p class="stat-label">Active Orders</p>
                <p class="stat-value">0</p>
                <p class="stat-change neutral">Currently in progress</p>
            </div>

            <div class="stat-card hover:shadow-md">
                <div class="stat-icon-container">
                    <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="stat-label">Completed</p>
                <p class="stat-value">0</p>
                <p class="stat-change neutral">Total orders done</p>
            </div>

            <div class="stat-card hover:shadow-md">
                <div class="stat-icon-container">
                    <svg class="stat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="stat-label">Total Spent</p>
                <p class="stat-value">₱0</p>
                <p class="stat-change neutral">Lifetime spending</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card-grid card-grid-4">
            <a href="{{ route('user.booking') }}" class="action-card">
                <div class="action-card-icon">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="action-card-title">Book Laundry</h3>
                <p class="action-card-description">Schedule a new pickup</p>
            </a>

            <a href="{{ route('user.status') }}" class="action-card">
                <div class="action-card-icon">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="action-card-title">Track Status</h3>
                <p class="action-card-description">Check order progress</p>
            </a>

            <a href="{{ route('user.route-to-admin') }}" class="action-card">
                <div class="action-card-icon">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="action-card-title">Find Shops</h3>
                <p class="action-card-description">Locate nearby stores</p>
            </a>

            <a href="{{ route('user.history') }}" class="action-card">
                <div class="action-card-icon">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="action-card-title">History</h3>
                <p class="action-card-description">View past orders</p>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="card p-8">
            <div class="flex items-center gap-3 mb-6">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-900">Recent Activity</h2>
            </div>
            <div class="empty-state">
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="empty-state-title">No recent activity yet</h3>
                <p class="empty-state-description">Your bookings will appear here</p>
            </div>
        </div>
    </div>
</x-layout>
