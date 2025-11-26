<x-layout>
    <x-slot name="title">Laundry Status</x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                    <span class="text-5xl">📊</span>
                </div>
                <div>
                    <h1 class="text-4xl font-bold mb-2">Laundry Status</h1>
                    <p class="text-lg opacity-90">Track your orders in real-time</p>
                </div>
            </div>
        </div>

        <!-- Status Cards -->
        <div class="space-y-4">
            @forelse($bookings as $index => $booking)
                <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-100 hover:border-green-300 hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <!-- Order Number & Status -->
                            <div class="flex items-center gap-4">
                                <div class="bg-gradient-to-br from-green-500 to-teal-500 rounded-full w-16 h-16 flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                    #{{ $index + 1 }}
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-1">Order #{{ $booking->id ?? $index + 1 }}</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @if($booking->status === 'Pending')
                                            <span class="inline-flex items-center gap-1 px-4 py-2 bg-yellow-100 text-yellow-700 rounded-full font-semibold text-sm">
                                                <span class="text-lg">⏳</span> Pending
                                            </span>
                                        @elseif($booking->status === 'In Progress')
                                            <span class="inline-flex items-center gap-1 px-4 py-2 bg-blue-100 text-blue-700 rounded-full font-semibold text-sm">
                                                <span class="text-lg">🔄</span> In Progress
                                            </span>
                                        @elseif($booking->status === 'Completed')
                                            <span class="inline-flex items-center gap-1 px-4 py-2 bg-green-100 text-green-700 rounded-full font-semibold text-sm">
                                                <span class="text-lg">✅</span> Completed
                                            </span>
                                        @elseif($booking->status === 'Delivered')
                                            <span class="inline-flex items-center gap-1 px-4 py-2 bg-purple-100 text-purple-700 rounded-full font-semibold text-sm">
                                                <span class="text-lg">🚚</span> Delivered
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-full font-semibold text-sm">
                                                <span class="text-lg">❓</span> Unknown
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Date & Time -->
                            <div class="flex gap-6 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">📅</span>
                                    <div>
                                        <p class="text-gray-500 text-xs">Date</p>
                                        <p class="font-semibold text-gray-800">{{ $booking->date }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">⏰</span>
                                    <div>
                                        <p class="text-gray-500 text-xs">Time</p>
                                        <p class="font-semibold text-gray-800">{{ $booking->time }}</p>
                                    </div>
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
                                    <span class="px-4 py-2 bg-gradient-to-r from-green-50 to-teal-50 border border-green-200 text-green-700 rounded-lg font-medium text-sm">
                                        {{ $service['name'] ?? 'Service' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center border-2 border-dashed border-gray-300">
                    <span class="text-8xl mb-6 block">📭</span>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No Active Orders</h3>
                    <p class="text-gray-600 mb-6">You haven't placed any bookings yet</p>
                    <a href="{{ route('user.booking') }}" class="inline-block">
                        <button class="btn btn-primary bg-gradient-to-r from-green-600 to-teal-600 border-none hover:scale-105 transition-transform">
                            🧺 Book Your First Laundry
                        </button>
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>
