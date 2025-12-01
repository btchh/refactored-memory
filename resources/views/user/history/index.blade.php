<x-layout>
    <x-slot name="title">Booking History</x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-8 text-white">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 backdrop-blur rounded-xl p-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-1">Booking History</h1>
                    <p class="text-white/80">Track all your laundry orders</p>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                @php
                    $totalOrders = count($bookings);
                    $completedOrders = collect($bookings)->where('status', 'completed')->count();
                    $pendingOrders = collect($bookings)->where('status', 'pending')->count();
                    $totalSpent = collect($bookings)->sum('total');
                @endphp
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-white/70 text-sm">Total Orders</p>
                    <p class="text-2xl font-bold">{{ $totalOrders }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-white/70 text-sm">Completed</p>
                    <p class="text-2xl font-bold">{{ $completedOrders }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-white/70 text-sm">Pending</p>
                    <p class="text-2xl font-bold">{{ $pendingOrders }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-white/70 text-sm">Total Spent</p>
                    <p class="text-2xl font-bold">₱{{ number_format($totalSpent, 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Search orders..." 
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all"
                        >
                    </div>
                </div>

                <!-- Status Filter Pills -->
                <div class="flex flex-wrap gap-2">
                    <button data-filter="all" class="filter-btn px-4 py-2.5 bg-primary-600 text-white rounded-xl font-medium text-sm transition-all">
                        All
                    </button>
                    <button data-filter="pending" class="filter-btn px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-medium text-sm hover:bg-yellow-50 hover:text-yellow-700 transition-all">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full inline-block mr-2"></span>Pending
                    </button>
                    <button data-filter="in_progress" class="filter-btn px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-medium text-sm hover:bg-blue-50 hover:text-blue-700 transition-all">
                        <span class="w-2 h-2 bg-blue-500 rounded-full inline-block mr-2"></span>In Progress
                    </button>
                    <button data-filter="completed" class="filter-btn px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-medium text-sm hover:bg-green-50 hover:text-green-700 transition-all">
                        <span class="w-2 h-2 bg-green-500 rounded-full inline-block mr-2"></span>Completed
                    </button>
                    <button data-filter="cancelled" class="filter-btn px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-medium text-sm hover:bg-red-50 hover:text-red-700 transition-all">
                        <span class="w-2 h-2 bg-red-500 rounded-full inline-block mr-2"></span>Cancelled
                    </button>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div id="bookings-container" class="space-y-4">
            @forelse($bookings as $index => $booking)
                @php
                    $statusConfig = [
                        'pending' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'badge' => 'bg-yellow-100 text-yellow-700', 'icon' => 'text-yellow-500'],
                        'in_progress' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'badge' => 'bg-blue-100 text-blue-700', 'icon' => 'text-blue-500'],
                        'completed' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'badge' => 'bg-green-100 text-green-700', 'icon' => 'text-green-500'],
                        'cancelled' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'badge' => 'bg-red-100 text-red-700', 'icon' => 'text-red-500'],
                    ];
                    $config = $statusConfig[$booking['status']] ?? $statusConfig['pending'];
                @endphp
                <div class="booking-card group bg-white rounded-xl border border-gray-200 hover:border-primary-300 hover:shadow-lg transition-all duration-300 overflow-hidden" 
                     data-status="{{ $booking['status'] }}"
                     data-order-id="{{ $booking['id'] ?? $index + 1 }}"
                     data-services="{{ collect($booking['services'])->pluck('name')->implode(' ') }}">
                    
                    <!-- Status Bar -->
                    <div class="h-1 {{ str_replace('text', 'bg', $config['icon']) }}"></div>
                    
                    <div class="p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                            <!-- Order Info -->
                            <div class="flex-1">
                                <div class="flex items-start gap-4">
                                    <!-- Order Number Badge -->
                                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-primary-200 flex-shrink-0">
                                        {{ $booking['id'] ?? $index + 1 }}
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-bold text-gray-900">Order #{{ $booking['id'] ?? $index + 1 }}</h3>
                                            <span class="px-3 py-1 {{ $config['badge'] }} rounded-full text-xs font-semibold uppercase tracking-wide">
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
                                            @if(isset($booking['service_type']))
                                            <div class="flex items-center gap-2">
                                                @if($booking['service_type'] === 'pickup')
                                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                    </svg>
                                                    <span class="text-blue-600">Home Pickup</span>
                                                @else
                                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                    <span class="text-green-600">Self Drop-off</span>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Services Tags -->
                                        <div class="flex flex-wrap gap-2 mt-4">
                                            @foreach($booking['services'] as $service)
                                                <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                                                    {{ $service['name'] }}
                                                    <span class="ml-2 text-gray-400">₱{{ $service['price'] }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Price & Actions -->
                            <div class="flex lg:flex-col items-center lg:items-end justify-between lg:justify-start gap-4 pt-4 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                                <div class="text-right">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total</p>
                                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($booking['total'] ?? 0, 2) }}</p>
                                </div>
                                
                                @if($booking['status'] === 'completed')
                                <a href="{{ route('user.booking.receipt', $booking['id']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Receipt
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div id="empty-state" class="bg-white rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Orders Yet</h3>
                    <p class="text-gray-500 mb-6">Start your first laundry order today!</p>
                    <a href="{{ route('user.booking') }}" class="btn btn-primary">
                        Book Now
                    </a>
                </div>
            @endforelse

            <!-- No Results -->
            <div id="no-results" class="hidden bg-white rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Results Found</h3>
                <p class="text-gray-500">Try adjusting your search or filter</p>
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

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => {
                        b.classList.remove('bg-primary-600', 'text-white');
                        b.classList.add('bg-gray-100', 'text-gray-600');
                    });
                    this.classList.remove('bg-gray-100', 'text-gray-600');
                    this.classList.add('bg-primary-600', 'text-white');
                    
                    currentFilter = this.dataset.filter;
                    filterBookings();
                });
            });

            searchInput.addEventListener('input', filterBookings);

            function filterBookings() {
                const searchTerm = searchInput.value.toLowerCase();
                let visibleCount = 0;

                bookingCards.forEach(card => {
                    const status = card.dataset.status;
                    const orderId = card.dataset.orderId.toString();
                    const services = card.dataset.services.toLowerCase();
                    
                    const matchesFilter = currentFilter === 'all' || status === currentFilter;
                    const matchesSearch = !searchTerm || orderId.includes(searchTerm) || services.includes(searchTerm);

                    if (matchesFilter && matchesSearch) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                noResults.classList.toggle('hidden', visibleCount > 0 || bookingCards.length === 0);
            }
        });
    </script>
</x-layout>
