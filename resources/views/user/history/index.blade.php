<x-layout>
    <x-slot name="title">Booking History</x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <x-modules.card class="p-8">
            <div class="flex items-center gap-4">
                <div class="bg-primary-50 rounded-full p-4">
                    <svg class="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Booking History</h1>
                    <p class="text-lg text-gray-600">View all your past orders</p>
                </div>
            </div>
        </x-modules.card>

        <!-- Search and Filter -->
        <x-modules.card class="p-6">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Search by order ID or service..." 
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="flex flex-wrap gap-2">
                    <button data-filter="all" class="filter-btn px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors">
                        All
                    </button>
                    <button data-filter="pending" class="filter-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                        Pending
                    </button>
                    <button data-filter="in_progress" class="filter-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                        In Progress
                    </button>
                    <button data-filter="completed" class="filter-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                        Completed
                    </button>
                    <button data-filter="cancelled" class="filter-btn px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                        Cancelled
                    </button>
                </div>
            </div>
        </x-modules.card>


        <!-- History Timeline -->
        <div id="bookings-container" class="space-y-4">
            @forelse($bookings as $index => $booking)
                <div class="booking-card bg-white rounded-lg border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden" 
                     data-status="{{ $booking['status'] }}"
                     data-order-id="{{ $booking['id'] ?? $index + 1 }}"
                     data-services="{{ collect($booking['services'])->pluck('name')->implode(' ') }}">
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                            <!-- Left Section -->
                            <div class="flex items-start gap-4">
                                <div class="bg-primary-600 rounded-full w-16 h-16 flex items-center justify-center text-white font-bold text-xl flex-shrink-0">
                                    #{{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">Order #{{ $booking['id'] ?? $index + 1 }}</h3>
                                    <div class="flex flex-wrap gap-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-gray-600">{{ $booking['date'] }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-gray-600">{{ $booking['time'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Section -->
                            <div class="flex flex-col items-end gap-3">
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
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Total Amount</p>
                                    <p class="text-2xl font-bold text-success">₱{{ $booking['total'] ?? '0' }}</p>
                                </div>
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
                                        {{ $service['name'] }} - ₱{{ $service['price'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div id="empty-state" class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No Order History</h3>
                    <p class="text-gray-600 mb-6">You haven't completed any orders yet</p>
                    <a href="{{ route('user.booking') }}" class="inline-block">
                        <button class="btn btn-primary">
                            Start Your First Order
                        </button>
                    </a>
                </div>
            @endforelse

            <!-- No Results Message (hidden by default) -->
            <div id="no-results" class="hidden bg-white rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No Results Found</h3>
                <p class="text-gray-600">Try adjusting your search or filter</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const filterBtns = document.querySelectorAll('.filter-btn');
            const bookingCards = document.querySelectorAll('.booking-card');
            const noResults = document.getElementById('no-results');
            
            let currentFilter = 'all';

            // Filter buttons
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => {
                        b.classList.remove('bg-primary-600', 'text-white');
                        b.classList.add('bg-gray-100', 'text-gray-700');
                    });
                    this.classList.remove('bg-gray-100', 'text-gray-700');
                    this.classList.add('bg-primary-600', 'text-white');
                    
                    currentFilter = this.dataset.filter;
                    filterBookings();
                });
            });

            // Search input
            searchInput.addEventListener('input', filterBookings);

            function filterBookings() {
                const searchTerm = searchInput.value.toLowerCase();
                let visibleCount = 0;

                bookingCards.forEach(card => {
                    const status = card.dataset.status;
                    const orderId = card.dataset.orderId.toString();
                    const services = card.dataset.services.toLowerCase();
                    
                    const matchesFilter = currentFilter === 'all' || status === currentFilter;
                    const matchesSearch = !searchTerm || 
                        orderId.includes(searchTerm) || 
                        services.includes(searchTerm);

                    if (matchesFilter && matchesSearch) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // Show/hide no results message
                if (visibleCount === 0 && bookingCards.length > 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }
        });
    </script>
</x-layout>
