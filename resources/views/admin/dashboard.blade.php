<x-layout>
    <x-slot:title>Admin Dashboard</x-slot:title>

    <div class="space-y-6">
        <!-- Header -->
        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600 mt-1">Welcome back, <strong>{{ Auth::guard('admin')->user()->admin_name }}</strong></p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Users -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ \App\Models\User::count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ \App\Models\User::where('status', 'active')->count() }} active</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Bookings -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ \App\Models\Transaction::count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ \App\Models\Transaction::where('status', 'pending')->count() }} pending</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-info/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ number_format(\App\Models\Transaction::where('status', '!=', 'cancelled')->sum('total_price'), 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">All time</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-success/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">System Status</p>
                        <p class="text-2xl font-bold text-success mt-2">Online</p>
                        <p class="text-xs text-gray-500 mt-1">All systems operational</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-success/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('admin.bookings.manage') }}" class="action-card">
                <div class="action-card-icon">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="action-card-title">Manage Bookings</h3>
                <p class="action-card-description">Update laundry booking status</p>
            </a>

            <a href="{{ route('admin.users.index') }}" class="action-card">
                <div class="action-card-icon">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="action-card-title">User Management</h3>
                <p class="action-card-description">Manage customer accounts</p>
            </a>

            <a href="{{ route('admin.pricing.index') }}" class="action-card">
                <div class="action-card-icon">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="action-card-title">Pricing</h3>
                <p class="action-card-description">Manage services and products</p>
            </a>

            <a href="{{ route('admin.analytics.index') }}" class="action-card">
                <div class="action-card-icon">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="action-card-title">Analytics</h3>
                <p class="action-card-description">View business insights</p>
            </a>

            <a href="{{ route('admin.revenue.index') }}" class="action-card">
                <div class="action-card-icon">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="action-card-title">Revenue Report</h3>
                <p class="action-card-description">View and print revenue reports</p>
            </a>
        </div>

        <!-- Admin Profile -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Your Profile</h2>
            </div>
            <div class="card-body">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 rounded-full bg-primary-100 flex items-center justify-center">
                            <span class="text-3xl font-bold text-primary-600">
                                {{ strtoupper(substr(Auth::guard('admin')->user()->admin_name, 0, 2)) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Admin Name</p>
                            <p class="text-base font-semibold text-gray-900 mt-1">{{ Auth::guard('admin')->user()->admin_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Email</p>
                            <p class="text-base font-semibold text-gray-900 mt-1">{{ Auth::guard('admin')->user()->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Phone</p>
                            <p class="text-base font-semibold text-gray-900 mt-1">{{ Auth::guard('admin')->user()->phone }}</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0 flex flex-col gap-2">
                        <a href="{{ route('admin.profile') }}" class="btn btn-primary">
                            Edit Profile
                        </a>
                        <a href="{{ route('admin.change-password') }}" class="btn btn-outline">
                            Change Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
