<x-layout>
    <x-slot name="title">Booking History</x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        @php
            $totalOrders = count($bookings);
            $completedOrders = collect($bookings)->where('status', 'completed')->count();
            $pendingOrders = collect($bookings)->where('status', 'pending')->count();
            $inProgressOrders = collect($bookings)->where('status', 'in_progress')->count();
            $cancelledOrders = collect($bookings)->where('status', 'cancelled')->count();
            $totalSpent = collect($bookings)->sum('total');
        @endphp
        <x-modules.page-header
            title="Booking History"
            subtitle="Track all your laundry orders"
            icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
            gradient="primary"
        >
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
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
        </x-modules.page-header>

        <!-- Search and Filter -->
        <x-modules.filter-panel
            :status-filters="[
                ['key' => 'all', 'label' => 'All', 'count' => $totalOrders, 'color' => 'primary', 'icon' => 'list'],
                ['key' => 'pending', 'label' => 'Pending', 'count' => $pendingOrders, 'color' => 'yellow'],
                ['key' => 'in_progress', 'label' => 'In Progress', 'count' => $inProgressOrders, 'color' => 'blue'],
                ['key' => 'completed', 'label' => 'Completed', 'count' => $completedOrders, 'color' => 'green'],
                ['key' => 'cancelled', 'label' => 'Cancelled', 'count' => $cancelledOrders, 'color' => 'red'],
            ]"
            current-status="all"
            :show-search="true"
            search-id="search-input"
            search-placeholder="Search by order ID or service name..."
            :client-side="true"
        />

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
    <div id="receipt-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeReceiptModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Booking Receipt</h3>
                    <button type="button" onclick="closeReceiptModal()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div id="receipt-content" class="p-6 max-h-[70vh] overflow-y-auto">
                    <div class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex gap-3 p-6 border-t border-gray-100">
                    <button type="button" onclick="closeReceiptModal()" class="flex-1 btn btn-outline rounded-xl">Close</button>
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Services</label>
                        <div class="bg-gray-50 rounded-xl p-3 space-y-2">
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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Products</label>
                        <div class="bg-gray-50 rounded-xl p-3 space-y-2">
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
                <div class="space-y-5">
                    <!-- Order ID & Status -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Order ID</label>
                            <span class="text-lg font-bold text-gray-900">#${String(booking.id).padStart(6, '0')}</span>
                        </div>
                        <span class="px-3 py-1.5 ${statusClass} rounded-full text-xs font-semibold uppercase tracking-wide">
                            ${statusText}
                        </span>
                    </div>

                    <!-- Booking Details -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Booking Details</label>
                        <div class="bg-gray-50 rounded-xl p-3 space-y-2 text-sm">
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
                    <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700">Total Amount</span>
                            <span class="text-2xl font-bold text-green-600">₱${parseFloat(booking.total || 0).toFixed(2)}</span>
                        </div>
                    </div>
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

            // Filter button color configurations
            const filterStyles = {
                all: { active: 'bg-primary-600 text-white shadow-lg shadow-primary-200', inactive: 'bg-gray-100 text-gray-600 hover:bg-primary-50 hover:text-primary-700' },
                pending: { active: 'bg-yellow-500 text-white shadow-lg shadow-yellow-200', inactive: 'bg-gray-100 text-gray-600 hover:bg-yellow-50 hover:text-yellow-700' },
                in_progress: { active: 'bg-blue-500 text-white shadow-lg shadow-blue-200', inactive: 'bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-700' },
                completed: { active: 'bg-green-500 text-white shadow-lg shadow-green-200', inactive: 'bg-gray-100 text-gray-600 hover:bg-green-50 hover:text-green-700' },
                cancelled: { active: 'bg-red-500 text-white shadow-lg shadow-red-200', inactive: 'bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-700' }
            };

            function updateFilterButtons() {
                filterBtns.forEach(btn => {
                    const filter = btn.dataset.filter;
                    const styles = filterStyles[filter] || filterStyles.all;
                    const isActive = filter === currentFilter;
                    
                    // Remove all possible classes
                    btn.classList.remove(
                        'bg-primary-600', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500', 'bg-red-500',
                        'text-white', 'shadow-lg', 'shadow-primary-200', 'shadow-yellow-200', 'shadow-blue-200', 'shadow-green-200', 'shadow-red-200',
                        'bg-gray-100', 'text-gray-600', 'hover:bg-gray-200',
                        'hover:bg-primary-50', 'hover:text-primary-700',
                        'hover:bg-yellow-50', 'hover:text-yellow-700',
                        'hover:bg-blue-50', 'hover:text-blue-700',
                        'hover:bg-green-50', 'hover:text-green-700',
                        'hover:bg-red-50', 'hover:text-red-700'
                    );
                    
                    // Add appropriate classes
                    const classesToAdd = isActive ? styles.active : styles.inactive;
                    classesToAdd.split(' ').forEach(cls => btn.classList.add(cls));
                    
                    // Update count badge
                    const badge = btn.querySelector('span > span:last-child');
                    if (badge && badge.classList.contains('rounded-full')) {
                        badge.classList.remove('bg-white/20', 'bg-gray-200', 'bg-yellow-100', 'bg-blue-100', 'bg-green-100', 'bg-red-100', 'group-hover:bg-yellow-100', 'group-hover:bg-blue-100', 'group-hover:bg-green-100', 'group-hover:bg-red-100', 'group-hover:bg-primary-100');
                        if (isActive) {
                            badge.classList.add('bg-white/20');
                        } else {
                            badge.classList.add('bg-gray-200');
                        }
                    }
                });
            }

            function filterBookings() {
                const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                let visibleCount = 0;

                bookingCards.forEach(card => {
                    const status = card.dataset.status;
                    const orderId = card.dataset.orderId;
                    const services = card.dataset.services?.toLowerCase() || '';
                    
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
                if (noResults) {
                    noResults.classList.toggle('hidden', visibleCount > 0);
                }
            }

            // Filter button click handlers
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    currentFilter = this.dataset.filter;
                    updateFilterButtons();
                    filterBookings();
                });
            });

            // Search input handler
            if (searchInput) {
                searchInput.addEventListener('input', filterBookings);
            }

            // Clear filters button
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    if (searchInput) searchInput.value = '';
                    currentFilter = 'all';
                    updateFilterButtons();
                    filterBookings();
                });
            }
        });
    </script>
</x-layout>
