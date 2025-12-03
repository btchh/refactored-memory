<x-layout>
    <x-slot name="title">Laundry Status</x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <x-modules.card class="p-8">
            <div class="flex items-center gap-4">
                <div class="bg-primary-50 rounded-full p-4">
                    <svg class="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Laundry Status</h1>
                    <p class="text-lg text-gray-600">Track your orders in real-time</p>
                </div>
            </div>
        </x-modules.card>

        <!-- Status Cards -->
        <div class="space-y-4">
            @forelse($bookings as $index => $booking)
                <div class="bg-white rounded-lg border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <!-- Order Number & Status -->
                            <div class="flex items-center gap-4">
                                <div class="bg-primary-600 rounded-full w-16 h-16 flex items-center justify-center text-white font-bold text-xl">
                                    #{{ $index + 1 }}
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-1">Order #{{ $booking['id'] ?? $index + 1 }}</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @php
                                            $statusBadges = [
                                                'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'Pending'],
                                                'in_progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'In Progress'],
                                                'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Completed'],
                                                'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Cancelled'],
                                            ];
                                            $status = $statusBadges[$booking['status']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => ucfirst($booking['status'])];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 {{ $status['bg'] }} {{ $status['text'] }} rounded-full font-semibold text-xs uppercase tracking-wide">
                                            {{ $status['label'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Date & Time -->
                            <div class="flex gap-6 text-sm">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="text-gray-500 text-xs">Date</p>
                                        <p class="font-semibold text-gray-800">{{ $booking['date'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-gray-500 text-xs">Time</p>
                                        <p class="font-semibold text-gray-800">{{ $booking['time'] }}</p>
                                    </div>
                                </div>
                                @if(isset($booking['branch_name']))
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <div>
                                        <p class="text-gray-500 text-xs">Branch</p>
                                        <p class="font-semibold text-purple-600">{{ $booking['branch_name'] }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Services -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-sm font-semibold text-gray-600 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Services:
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($booking['services'] as $service)
                                    <span class="px-3 py-1 bg-gray-100 border border-gray-200 text-gray-700 rounded-lg font-medium text-sm">
                                        {{ $service['name'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No Active Orders</h3>
                    <p class="text-gray-600 mb-6">You haven't placed any bookings yet</p>
                    <a href="{{ route('user.booking') }}" class="inline-block">
                        <button class="btn btn-primary">
                            Book Your First Laundry
                        </button>
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>
