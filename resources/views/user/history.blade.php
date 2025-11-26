<x-layout>
    <x-slot name="title">Booking History</x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                    <span class="text-5xl">📜</span>
                </div>
                <div>
                    <h1 class="text-4xl font-bold mb-2">Booking History</h1>
                    <p class="text-lg opacity-90">View all your past orders</p>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
            <div class="flex flex-wrap gap-3">
                <button class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg font-semibold hover:scale-105 transition-transform">
                    All Orders
                </button>
                <button class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-colors">
                    Completed
                </button>
                <button class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition-colors">
                    Cancelled
                </button>
            </div>
        </div>

        <!-- History Timeline -->
        <div class="space-y-4">
            @forelse($bookings as $index => $booking)
                <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-100 hover:border-purple-300 hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                            <!-- Left Section -->
                            <div class="flex items-start gap-4">
                                <div class="bg-gradient-to-br from-purple-500 to-indigo-500 rounded-full w-16 h-16 flex items-center justify-center text-white font-bold text-xl shadow-lg flex-shrink-0">
                                    #{{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">Order #{{ $booking->id ?? $index + 1 }}</h3>
                                    <div class="flex flex-wrap gap-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">📅</span>
                                            <span class="text-gray-600">{{ $booking->date }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">⏰</span>
                                            <span class="text-gray-600">{{ $booking->time }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Section -->
                            <div class="flex flex-col items-end gap-3">
                                @if($booking->status === 'Delivered')
                                    <span class="inline-flex items-center gap-2 px-5 py-2 bg-green-100 text-green-700 rounded-full font-semibold">
                                        <span class="text-lg">✅</span> Delivered
                                    </span>
                                @elseif($booking->status === 'Cancelled')
                                    <span class="inline-flex items-center gap-2 px-5 py-2 bg-red-100 text-red-700 rounded-full font-semibold">
                                        <span class="text-lg">❌</span> Cancelled
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-5 py-2 bg-gray-100 text-gray-700 rounded-full font-semibold">
                                        <span class="text-lg">📦</span> {{ $booking->status }}
                                    </span>
                                @endif
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Total Amount</p>
                                    <p class="text-2xl font-bold text-purple-600">₱{{ number_format($booking->total ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Services -->
                        @if(!empty($booking->services) && is_array($booking->services))
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm font-semibold text-gray-600 mb-3 flex items-center gap-2">
                                <span class="text-lg">✨</span> Services:
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($booking->services as $service)
                                    <span class="px-4 py-2 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 text-purple-700 rounded-lg font-medium text-sm">
                                        {{ $service['name'] ?? 'Service' }} - ₱{{ $service['price'] ?? 0 }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Actions -->
                        <div class="mt-6 flex flex-wrap gap-3">
                            <button class="btn btn-sm btn-outline hover:bg-purple-500 hover:text-white hover:border-purple-500 transition-colors">
                                📄 View Receipt
                            </button>
                            <button class="btn btn-sm btn-outline hover:bg-indigo-500 hover:text-white hover:border-indigo-500 transition-colors">
                                🔄 Reorder
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center border-2 border-dashed border-gray-300">
                    <span class="text-8xl mb-6 block">📭</span>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No Order History</h3>
                    <p class="text-gray-600 mb-6">You haven't completed any orders yet</p>
                    <a href="{{ route('user.booking') }}" class="inline-block">
                        <button class="btn btn-primary bg-gradient-to-r from-purple-600 to-indigo-600 border-none hover:scale-105 transition-transform">
                            🧺 Start Your First Order
                        </button>
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>
