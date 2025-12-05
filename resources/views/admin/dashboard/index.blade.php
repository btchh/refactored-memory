<x-layout>
    <x-slot:title>Admin Dashboard</x-slot:title>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Hero Header -->
        <div class="relative bg-gradient-to-br from-wash via-wash-dark to-gray-900 rounded-2xl p-12 overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
            </div>
            <div class="relative">
                <h1 class="text-5xl font-black text-white mb-3">Admin Dashboard</h1>
                <p class="text-xl text-white/80">Welcome back, {{ Auth::guard('admin')->user()->fname }} {{ Auth::guard('admin')->user()->lname }}</p>
                <p class="text-white/60 mt-2">{{ now()->format('l, F d, Y') }}</p>
            </div>
        </div>

        <!-- Stat Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total Bookings -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-info transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-info/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-info/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Total Bookings</p>
                    <p class="text-3xl font-black text-gray-900">{{ $pendingBookings + $inProgressBookings + $completedBookings }}</p>
                </div>
            </div>

            <!-- Revenue -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-wash transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-wash/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-wash/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Month Revenue</p>
                    <p class="text-3xl font-black text-gray-900">₱{{ number_format($monthRevenue, 0) }}</p>
                </div>
            </div>

            <!-- Users -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-success transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-success/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Total Users</p>
                    <p class="text-3xl font-black text-gray-900">{{ $totalCustomers }}</p>
                </div>
            </div>

            <!-- Active Orders -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-warning transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-warning/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-warning/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Active Orders</p>
                    <p class="text-3xl font-black text-gray-900">{{ $pendingBookings + $inProgressBookings }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Action Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- Bookings -->
            <a href="{{ route('admin.bookings.manage') }}" class="group flex flex-col items-center p-5 rounded-xl bg-info/5 hover:bg-info border-2 border-info/20 hover:border-info transition-all">
                <div class="w-14 h-14 bg-info rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-md">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-900 group-hover:text-white transition-colors">Bookings</span>
            </a>

            <!-- Users -->
            <a href="{{ route('admin.users.index') }}" class="group flex flex-col items-center p-5 rounded-xl bg-success/5 hover:bg-success border-2 border-success/20 hover:border-success transition-all">
                <div class="w-14 h-14 bg-success rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-md">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-900 group-hover:text-white transition-colors">Users</span>
            </a>

            <!-- Revenue -->
            <a href="{{ route('admin.revenue.index') }}" class="group flex flex-col items-center p-5 rounded-xl bg-wash/5 hover:bg-wash border-2 border-wash/20 hover:border-wash transition-all">
                <div class="w-14 h-14 bg-wash rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-md">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-900 group-hover:text-white transition-colors">Revenue</span>
            </a>

            <!-- Delivery Routes -->
            <a href="{{ route('admin.route-to-user') }}" class="group flex flex-col items-center p-5 rounded-xl bg-warning/5 hover:bg-warning border-2 border-warning/20 hover:border-warning transition-all">
                <div class="w-14 h-14 bg-warning rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-md">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-900 group-hover:text-white transition-colors">Delivery</span>
            </a>
        </div>

        <!-- Revenue Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-success to-success/80 rounded-2xl p-6 text-white shadow-lg">
                <p class="text-white/80 text-sm font-bold mb-2">Today's Revenue</p>
                <p class="text-4xl font-black">₱{{ number_format($todayRevenue, 2) }}</p>
            </div>
            <div class="bg-gradient-to-br from-info to-info/80 rounded-2xl p-6 text-white shadow-lg">
                <p class="text-white/80 text-sm font-bold mb-2">This Week</p>
                <p class="text-4xl font-black">₱{{ number_format($weekRevenue, 2) }}</p>
            </div>
            <div class="bg-gradient-to-br from-wash to-wash-dark rounded-2xl p-6 text-white shadow-lg">
                <p class="text-white/80 text-sm font-bold mb-2">This Month</p>
                <p class="text-4xl font-black">₱{{ number_format($monthRevenue, 2) }}</p>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Today's Schedule -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-black text-gray-900">Today's Schedule</h2>
                    <span class="text-sm font-bold text-gray-600">{{ $todayBookings }} bookings</span>
                </div>
                
                @if($todaySchedule->count() > 0)
                    <div class="space-y-3">
                        @foreach($todaySchedule as $booking)
                            @php
                                $statusColors = [
                                    'pending' => 'bg-warning/10 text-warning',
                                    'in_progress' => 'bg-info/10 text-info',
                                    'completed' => 'bg-success/10 text-success',
                                    'cancelled' => 'bg-error/10 text-error',
                                ];
                                $statusColor = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border-2 border-gray-100">
                                <div class="text-center min-w-[60px]">
                                    <p class="text-sm font-black text-wash">{{ $booking->formatted_time }}</p>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-900 truncate">
                                        @if($booking->user)
                                            {{ $booking->user->fname }} {{ $booking->user->lname }}
                                        @else
                                            <span class="text-gray-500">Archived User</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-600 truncate">{{ $booking->services->pluck('service_name')->implode(', ') ?: 'No services' }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-black rounded-full uppercase {{ $statusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-600 font-semibold">No bookings scheduled for today</p>
                    </div>
                @endif
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-black text-gray-900">Recent Bookings</h2>
                    @if($recentBookings->count() > 0)
                        <a href="{{ route('admin.bookings.manage') }}" class="text-wash hover:text-wash-dark text-sm font-bold transition-colors">View All →</a>
                    @endif
                </div>
                
                @if($recentBookings->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentBookings as $booking)
                            @php
                                $statusColors = [
                                    'pending' => 'bg-warning/10 text-warning',
                                    'in_progress' => 'bg-info/10 text-info',
                                    'completed' => 'bg-success/10 text-success',
                                    'cancelled' => 'bg-error/10 text-error',
                                ];
                                $statusColor = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border-2 border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="bg-wash/10 rounded-full w-10 h-10 flex items-center justify-center">
                                        <span class="text-wash font-black text-xs">#{{ $booking->id }}</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">
                                            @if($booking->user)
                                                {{ $booking->user->fname }} {{ $booking->user->lname }}
                                            @else
                                                <span class="text-gray-500">Archived User</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-600">{{ $booking->formatted_date }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-2 py-1 text-xs font-black rounded-full uppercase {{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                    <p class="text-xs font-black text-gray-900 mt-1">₱{{ number_format($booking->total_price, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-gray-600 font-semibold">No bookings yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
