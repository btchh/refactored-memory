<x-layout>
    <x-slot:title>Admin Dashboard</x-slot:title>

    <div class="space-y-6">
        <!-- Welcome Banner -->
        <x-modules.page-header
            title="Welcome back, {{ Auth::guard('admin')->user()->admin_name }}!"
            subtitle="Here's your business overview for {{ now()->format('l, F d, Y') }}"
            icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
            gradient="primary"
        />

        <!-- Booking Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-yellow-100 rounded-full p-2">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Pending</p>
                        <p class="text-xl font-bold text-gray-900">{{ $pendingBookings }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-100 rounded-full p-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">In Progress</p>
                        <p class="text-xl font-bold text-gray-900">{{ $inProgressBookings }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-green-100 rounded-full p-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Completed</p>
                        <p class="text-xl font-bold text-gray-900">{{ $completedBookings }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-primary-100 rounded-full p-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Customers</p>
                        <p class="text-xl font-bold text-gray-900">{{ $totalCustomers }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-5 text-white">
                <p class="text-green-100 text-sm">Today's Revenue</p>
                <p class="text-3xl font-bold mt-1">₱{{ number_format($todayRevenue, 2) }}</p>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-5 text-white">
                <p class="text-blue-100 text-sm">This Week</p>
                <p class="text-3xl font-bold mt-1">₱{{ number_format($weekRevenue, 2) }}</p>
            </div>
            <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg p-5 text-white">
                <p class="text-primary-100 text-sm">This Month</p>
                <p class="text-3xl font-bold mt-1">₱{{ number_format($monthRevenue, 2) }}</p>
            </div>
        </div>


        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Today's Schedule -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Today's Schedule</h2>
                    <span class="text-sm text-gray-500">{{ $todayBookings }} bookings</span>
                </div>
                
                @if($todaySchedule->count() > 0)
                    <div class="space-y-3">
                        @foreach($todaySchedule as $booking)
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                ];
                                $statusColor = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="text-center min-w-[60px]">
                                    <p class="text-sm font-bold text-primary-600">{{ $booking->formatted_time }}</p>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 truncate">{{ $booking->user->fname }} {{ $booking->user->lname }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $booking->services->pluck('service_name')->implode(', ') ?: 'No services' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-500 text-sm">No bookings scheduled for today</p>
                    </div>
                @endif
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Recent Bookings</h2>
                    @if($recentBookings->count() > 0)
                        <a href="{{ route('admin.bookings.manage') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View All</a>
                    @endif
                </div>
                
                @if($recentBookings->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentBookings as $booking)
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                ];
                                $statusColor = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="bg-primary-100 rounded-full w-8 h-8 flex items-center justify-center">
                                        <span class="text-primary-600 font-bold text-xs">#{{ $booking->id }}</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $booking->user->fname }} {{ $booking->user->lname }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->formatted_date }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                    <p class="text-xs font-bold text-gray-900 mt-1">₱{{ number_format($booking->total_price, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-gray-500 text-sm">No bookings yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
