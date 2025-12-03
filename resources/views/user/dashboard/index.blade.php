<x-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <x-modules.page-header
            title="{{ Auth::user()->fname }} {{ Auth::user()->lname }}"
            subtitle="{{ now()->format('l, F d, Y') }}"
            icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
            gradient="primary"
        >
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-white/70 text-sm">Active Orders</p>
                    <p class="text-2xl font-bold">{{ $activeOrders }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-white/70 text-sm">Completed</p>
                    <p class="text-2xl font-bold">{{ $completedOrders }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-white/70 text-sm">Next Pickup</p>
                    <p class="text-2xl font-bold">{{ $nextBooking ? $nextBooking->booking_date->format('M d') : 'None' }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-white/70 text-sm">Total Spent</p>
                    <p class="text-2xl font-bold">₱{{ number_format($recentBookings->sum('total_price'), 0) }}</p>
                </div>
            </div>
        </x-modules.page-header>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="{{ route('user.booking') }}" class="flex flex-col items-center p-4 rounded-xl bg-primary-50 hover:bg-primary-100 transition-colors group">
                    <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Book Now</span>
                </a>
                <a href="{{ route('user.status') }}" class="flex flex-col items-center p-4 rounded-xl bg-blue-50 hover:bg-blue-100 transition-colors group">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Track Order</span>
                </a>
                <a href="{{ route('user.route-to-admin') }}" class="flex flex-col items-center p-4 rounded-xl bg-green-50 hover:bg-green-100 transition-colors group">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Find Shop</span>
                </a>
                <a href="{{ route('user.history') }}" class="flex flex-col items-center p-4 rounded-xl bg-yellow-50 hover:bg-yellow-100 transition-colors group">
                    <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">History</span>
                </a>
            </div>
        </div>
        <!-- Recent Orders -->
        @if($recentBookings->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Recent Orders</h2>
                <a href="{{ route('user.history') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View all</a>
            </div>
            <div class="space-y-3">
                @foreach($recentBookings as $order)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                            <span class="text-sm font-bold text-primary-600">#{{ $order->id }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $order->services->pluck('service_name')->join(', ') ?: 'Order' }}</p>
                            <p class="text-sm text-gray-500">{{ $order->booking_date?->format('M d, Y') ?? $order->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">₱{{ number_format($order->total_price, 2) }}</p>
                        <span class="text-xs px-2 py-1 rounded-full {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
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
