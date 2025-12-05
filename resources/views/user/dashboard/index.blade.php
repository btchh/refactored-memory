<x-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Hero Header -->
        <div class="relative bg-gradient-to-br from-wash via-wash-dark to-gray-900 rounded-2xl p-12 overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
            </div>
            <div class="relative">
                <h1 class="text-5xl font-black text-white mb-3">Welcome Back!</h1>
                <p class="text-xl text-white/80">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
                <p class="text-white/60 mt-2">{{ now()->format('l, F d, Y') }}</p>
            </div>
        </div>

        <!-- Stat Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <!-- Active Bookings Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-warning transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-warning/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-warning/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Active Bookings</p>
                    <p class="text-3xl font-black text-gray-900">{{ $activeOrders }}</p>
                </div>
            </div>

            <!-- Completed Orders Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-success transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-success/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Completed Orders</p>
                    <p class="text-3xl font-black text-gray-900">{{ $completedOrders }}</p>
                </div>
            </div>

            <!-- Next Booking Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-info transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-info/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-info/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Next Booking</p>
                    @if($nextBooking)
                        <p class="text-lg font-black text-gray-900">{{ $nextBooking->booking_date?->format('M d') ?? 'TBD' }}</p>
                        <p class="text-xs text-gray-600 font-medium">{{ $nextBooking->booking_time ?? 'Time TBD' }}</p>
                    @else
                        <p class="text-lg font-black text-gray-500">No upcoming</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Action Cards -->
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('user.booking') }}" class="group flex flex-col items-center p-6 rounded-xl bg-wash/5 hover:bg-wash border-2 border-wash/20 hover:border-wash transition-all">
                <div class="w-16 h-16 bg-wash rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-md">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="text-base font-bold text-gray-900 group-hover:text-white transition-colors">New Booking</span>
            </a>
            <a href="{{ route('user.history') }}" class="group flex flex-col items-center p-6 rounded-xl bg-info/5 hover:bg-info border-2 border-info/20 hover:border-info transition-all">
                <div class="w-16 h-16 bg-info rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform shadow-md">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span class="text-base font-bold text-gray-900 group-hover:text-white transition-colors">Order History</span>
            </a>
        </div>

        <!-- Recent Orders -->
        @if($recentBookings->count() > 0)
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-black text-gray-900">Recent Orders</h2>
                <a href="{{ route('user.history') }}" class="text-sm text-wash hover:text-wash-dark font-bold transition-colors">View all →</a>
            </div>
            <div class="space-y-3">
                @foreach($recentBookings as $order)
                <div class="flex items-center justify-between p-5 bg-gray-50 rounded-xl border-2 border-gray-100 hover:border-wash transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-wash/10 rounded-xl flex items-center justify-center">
                            <span class="text-sm font-black text-wash">#{{ $order->id }}</span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $order->services->pluck('service_name')->join(', ') ?: 'Order' }}</p>
                            <p class="text-sm text-gray-600 font-medium">{{ $order->booking_date?->format('M d, Y') ?? $order->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-black text-gray-900 text-lg">₱{{ number_format($order->total_price, 2) }}</p>
                        <span class="inline-block text-xs px-3 py-1 rounded-full font-bold uppercase mt-1 {{ $order->status === 'completed' ? 'bg-success/10 text-success' : ($order->status === 'pending' ? 'bg-warning/10 text-warning' : 'bg-info/10 text-info') }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-layout>
