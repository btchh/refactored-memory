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
        <x-modules.card class="p-6 print:hidden">
            {{-- Quick Filters --}}
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="text-xs font-medium text-gray-600">Quick:</span>
                <button data-filter="all" class="filter-btn px-2 py-1 text-xs rounded transition-colors bg-primary-100 text-primary-700">All</button>
                <button data-filter="pending" class="filter-btn px-2 py-1 text-xs rounded transition-colors bg-gray-100 hover:bg-gray-200 text-gray-700">Pending</button>
                <button data-filter="in_progress" class="filter-btn px-2 py-1 text-xs rounded transition-colors bg-gray-100 hover:bg-gray-200 text-gray-700">In Progress</button>
                <button data-filter="completed" class="filter-btn px-2 py-1 text-xs rounded transition-colors bg-gray-100 hover:bg-gray-200 text-gray-700">Completed</button>
                <button data-filter="cancelled" class="filter-btn px-2 py-1 text-xs rounded transition-colors bg-gray-100 hover:bg-gray-200 text-gray-700">Cancelled</button>
            </div>

            {{-- Search --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="form-group md:col-span-2 lg:col-span-3">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           id="search-input"
                           placeholder="Search orders..." 
                           class="form-input w-full">
                </div>
                <div class="form-group flex items-end">
                    <button type="button" id="clear-filters" class="btn btn-outline w-full">Clear</button>
                </div>
            </div>
        </x-modules.card>

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
                                    <p class="text-2xl font-bold text-gray-900">₱{{ number_format($booking['total'], 2) }}</p>
                                </div>
                                
                                @if($booking['status'] === 'completed')
                                <button type="button" 
                                        onclick="openReceiptModal({{ $booking['id'] }})"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    View Receipt
                                </button>
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

    <!-- Receipt Modal -->
    <div id="receipt-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeReceiptModal()"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full">
                <!-- Close Button -->
                <button onclick="closeReceiptModal()" class="absolute top-4 right-4 z-10 p-2 bg-white/80 hover:bg-white rounded-full shadow-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Receipt Content Container -->
                <div id="receipt-content">
                    <!-- Loading State -->
                    <div id="receipt-loading" class="p-12 text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
                        <p class="mt-4 text-gray-500">Loading receipt...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Booking data for receipts
        const bookingsData = @json($bookings);

        function openReceiptModal(bookingId) {
            const modal = document.getElementById('receipt-modal');
            const content = document.getElementById('receipt-content');
            const loading = document.getElementById('receipt-loading');
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Find booking data
            const booking = bookingsData.find(b => b.id === bookingId);
            
            if (booking) {
                content.innerHTML = generateReceiptHTML(booking);
            } else {
                content.innerHTML = '<div class="p-12 text-center text-red-500">Receipt not found</div>';
            }
        }

        function closeReceiptModal() {
            const modal = document.getElementById('receipt-modal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeReceiptModal();
        });

        function generateReceiptHTML(booking) {
            const statusClasses = {
                'completed': 'bg-green-100 text-green-700',
                'in_progress': 'bg-blue-100 text-blue-700',
                'cancelled': 'bg-red-100 text-red-700',
                'pending': 'bg-yellow-100 text-yellow-700'
            };
            const statusClass = statusClasses[booking.status] || 'bg-gray-100 text-gray-700';
            const statusText = booking.status.replace('_', ' ');

            let servicesHTML = '';
            if (booking.services && booking.services.length > 0) {
                servicesHTML = `
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Services
                        </h3>
                        <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                            ${booking.services.map(s => `
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">${s.name}</span>
                                    <span class="font-medium">₱${parseFloat(s.price).toFixed(2)}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            let productsHTML = '';
            if (booking.products && booking.products.length > 0) {
                productsHTML = `
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Products
                        </h3>
                        <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                            ${booking.products.map(p => `
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-700">${p.name}</span>
                                    <span class="font-medium">₱${parseFloat(p.price).toFixed(2)}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            return `
                <!-- Header -->
                <div class="bg-primary-600 text-white p-6 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 rounded-full mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold mb-1">Booking Receipt</h2>
                    <p class="text-primary-100 text-sm">Order #${String(booking.id).padStart(6, '0')}</p>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-5 max-h-[60vh] overflow-y-auto">
                    <!-- Status -->
                    <div class="flex items-center justify-between pb-4 border-b border-dashed">
                        <span class="text-sm text-gray-500">Status</span>
                        <span class="px-3 py-1 ${statusClass} rounded-full text-xs font-semibold uppercase tracking-wide">
                            ${statusText}
                        </span>
                    </div>

                    <!-- Booking Details -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Booking Details
                        </h3>
                        <div class="bg-gray-50 rounded-lg p-3 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Date</span>
                                <span class="font-medium">${booking.date}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Time</span>
                                <span class="font-medium">${booking.time}</span>
                            </div>
                            ${booking.service_type ? `
                            <div class="flex justify-between">
                                <span class="text-gray-500">Service Type</span>
                                <span class="font-medium">${booking.service_type === 'pickup' ? 'Home Pickup' : 'Self Drop-off'}</span>
                            </div>
                            ` : ''}
                            ${booking.branch_name ? `
                            <div class="flex justify-between">
                                <span class="text-gray-500">Branch</span>
                                <span class="font-medium">${booking.branch_name}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                    ${servicesHTML}
                    ${productsHTML}

                    <!-- Total -->
                    <div class="pt-4 border-t border-dashed">
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-700">Total Amount</span>
                                <span class="text-2xl font-bold text-green-600">₱${parseFloat(booking.total || 0).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 pb-6">
                    <button onclick="closeReceiptModal()" class="w-full btn btn-outline">Close</button>
                </div>
            `;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const filterBtns = document.querySelectorAll('.filter-btn');
            const bookingCards = document.querySelectorAll('.booking-card');
            const noResults = document.getElementById('no-results');
            const clearFiltersBtn = document.getElementById('clear-filters');
            
            let currentFilter = 'all';

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => {
                        b.classList.remove('bg-primary-100', 'text-primary-700');
                        b.classList.add('bg-gray-100', 'text-gray-700');
                    });
                    this.classList.remove('bg-gray-100', 'text-gray-700');
                    this.classList.add('bg-primary-100', 'text-primary-700');
                    
                    currentFilter = this.dataset.filter;
                    filterBookings();
                });
            });

            searchInput.addEventListener('input', filterBookings);

            clearFiltersBtn.addEventListener('click', function() {
                searchInput.value = '';
                filterBtns.forEach(b => {
                    b.classList.remove('bg-primary-100', 'text-primary-700');
                    b.classList.add('bg-gray-100', 'text-gray-700');
                });
                filterBtns[0].classList.remove('bg-gray-100', 'text-gray-700');
                filterBtns[0].classList.add('bg-primary-100', 'text-primary-700');
                currentFilter = 'all';
                filterBookings();
            });

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
