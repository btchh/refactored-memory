<x-layout>
    <x-slot name="title">Laundry Status</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        @php
            $totalOrders = count($bookings);
            $pendingOrders = collect($bookings)->where('status', 'pending')->count();
            $inProgressOrders = collect($bookings)->where('status', 'in_progress')->count();
            $completedOrders = collect($bookings)->where('status', 'completed')->count();
        @endphp

        <!-- Hero Header -->
        <div class="relative bg-gradient-to-br from-wash via-wash-dark to-gray-900 rounded-2xl p-12 overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
            </div>
            
            <!-- Content -->
            <div class="relative">
                <h1 class="text-5xl font-black text-white mb-3">Order Tracking</h1>
                <p class="text-xl text-white/80">Track your orders in real-time</p>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total Orders -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-wash transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-wash/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-wash/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Total Orders</p>
                    <p class="text-3xl font-black text-gray-900">{{ $totalOrders }}</p>
                </div>
            </div>

            <!-- Pending -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-warning transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-warning/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-warning/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Pending</p>
                    <p class="text-3xl font-black text-gray-900">{{ $pendingOrders }}</p>
                </div>
            </div>

            <!-- In Progress -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-info transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-info/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-info/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">In Progress</p>
                    <p class="text-3xl font-black text-gray-900">{{ $inProgressOrders }}</p>
                </div>
            </div>

            <!-- Completed -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-success transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-success/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Completed</p>
                    <p class="text-3xl font-black text-gray-900">{{ $completedOrders }}</p>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="space-y-4">
            @forelse($bookings as $index => $booking)
                <!-- Content Card for each order -->
                <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <!-- Order Number & Status -->
                        <div class="flex items-center gap-4">
                            <div class="bg-wash rounded-full w-16 h-16 flex items-center justify-center text-white font-black text-xl shadow-md">
                                #{{ $index + 1 }}
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 mb-2">Order #{{ $booking['id'] ?? $index + 1 }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $statusClass = match($booking['status']) {
                                            'pending' => 'badge-pending',
                                            'in_progress' => 'badge-in-progress',
                                            'completed' => 'badge-completed',
                                            'cancelled' => 'badge-cancelled',
                                            default => 'badge-pending'
                                        };
                                        $statusLabel = match($booking['status']) {
                                            'pending' => 'Pending',
                                            'in_progress' => 'In Progress',
                                            'completed' => 'Completed',
                                            'cancelled' => 'Cancelled',
                                            default => ucfirst($booking['status'])
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="flex flex-wrap gap-6 text-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="text-gray-500 text-xs font-bold uppercase">Date</p>
                                    <p class="font-bold text-gray-900">{{ $booking['date'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-gray-500 text-xs font-bold uppercase">Time</p>
                                    <p class="font-bold text-gray-900">{{ $booking['time'] }}</p>
                                </div>
                            </div>
                            @if(isset($booking['branch_name']))
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <div>
                                    <p class="text-gray-500 text-xs font-bold uppercase">Branch</p>
                                    <p class="font-bold text-wash">{{ $booking['branch_name'] }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="mt-6 pt-6 border-t-2 border-gray-200">
                        <p class="text-sm font-bold text-gray-600 mb-3 flex items-center gap-2 uppercase">
                            <svg class="w-4 h-4 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Services:
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($booking['services'] as $service)
                                <span class="px-3 py-2 bg-gray-100 border-2 border-gray-200 text-gray-900 rounded-xl font-semibold text-sm">
                                    {{ $service['name'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State Content Card -->
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-300 p-12 text-center">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="text-2xl font-black text-gray-900 mb-2">No Active Orders</h3>
                    <p class="text-gray-600 mb-6">You haven't placed any bookings yet</p>
                    <a href="{{ route('user.booking') }}" class="inline-block">
                        <button class="btn btn-primary btn-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Book Your First Laundry
                        </button>
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>
