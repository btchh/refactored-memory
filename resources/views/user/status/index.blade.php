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

        <!-- Order List -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-black text-gray-900">Active Orders</h2>
            </div>

            <div class="space-y-4">
                @forelse($bookings as $index => $booking)
                    <div class="booking-card group bg-white rounded-xl border-2 border-gray-200 hover:border-wash hover:shadow-lg transition-all overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                                <!-- Order Info -->
                                <div class="flex-1">
                                    <div class="flex items-start gap-4">
                                        <!-- Order Number Badge -->
                                        <div class="w-14 h-14 bg-gradient-to-br from-wash to-wash-dark rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg flex-shrink-0">
                                            {{ $booking['id'] ?? $index + 1 }}
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h3 class="text-lg font-bold text-gray-900">Order #{{ $booking['id'] ?? $index + 1 }}</h3>
                                                @php
                                                    $badgeClass = match($booking['status']) {
                                                        'pending' => 'badge badge-pending',
                                                        'in_progress' => 'badge badge-in-progress',
                                                        'completed' => 'badge badge-completed',
                                                        'cancelled' => 'badge badge-cancelled',
                                                        default => 'badge badge-pending'
                                                    };
                                                @endphp
                                                <span class="{{ $badgeClass }}">
                                                    {{ str_replace('_', ' ', $booking['status']) }}
                                                </span>
                                            </div>
                                        
                                            <!-- Date & Time -->
                                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>{{ $booking['date'] }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>{{ $booking['time'] }}</span>
                                                </div>
                                                @if(isset($booking['service_description']))
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                    </svg>
                                                    <span class="text-blue-600">{{ $booking['service_description'] }}</span>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Branch Info -->
                                            @if(isset($booking['branch_name']))
                                            <div class="flex items-center gap-2 mt-2 text-sm">
                                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                                <span class="text-purple-600 font-medium">{{ $booking['branch_name'] }}</span>
                                                @if(isset($booking['branch_address']))
                                                    <span class="text-gray-400">•</span>
                                                    <span class="text-gray-500 truncate max-w-xs" title="{{ $booking['branch_address'] }}">{{ Str::limit($booking['branch_address'], 30) }}</span>
                                                @endif
                                            </div>
                                            @endif
                                                
                                            <!-- Services Tags -->
                                            <div class="flex flex-wrap gap-2 mt-4">
                                                @foreach($booking['services'] as $service)
                                                    <span class="inline-flex items-center px-3 py-1.5 bg-wash/5 text-wash rounded-lg text-sm font-bold border border-wash/20">
                                                        {{ $service['name'] }}
                                                        @if(isset($service['price']))
                                                            <span class="ml-2 text-wash/70">₱{{ $service['price'] }}</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Price & Actions -->
                                <div class="flex lg:flex-col items-center lg:items-end justify-between lg:justify-start gap-4 pt-4 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                                    @if(isset($booking['total']))
                                    <div class="text-right">
                                        <p class="text-xs text-gray-600 font-bold uppercase tracking-wide mb-1">Total</p>
                                        <p class="text-3xl font-black text-gray-900">₱{{ number_format($booking['total'], 2) }}</p>
                                    </div>
                                    @endif
                                    
                                    @if($booking['status'] === 'pending')
                                        <button onclick="cancelUserBooking({{ $booking['id'] }})" class="btn btn-error">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Cancel Order
                                        </button>
                                    @endif
                                </div>
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

    @push('scripts')
    <script>
        // Make cancel function available globally
        window.cancelUserBooking = function(bookingId) {
            if (typeof window.showCancelModal === 'function') {
                window.showCancelModal(bookingId, {
                    type: 'user',
                    cancelUrl: '/user/api/bookings/__ID__/cancel',
                    csrfToken: '{{ csrf_token() }}',
                    onSuccess: () => {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                });
            } else {
                console.error('showCancelModal function not available');
                alert('Cancel function not loaded. Please refresh the page.');
            }
        };
    </script>
    @endpush
</x-layout>
