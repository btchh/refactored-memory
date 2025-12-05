<x-layout>
    <x-slot name="title">Order History</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        @php
            $totalOrders = count($bookings);
            $completedOrders = collect($bookings)->where('status', 'completed')->count();
            $pendingOrders = collect($bookings)->where('status', 'pending')->count();
            $inProgressOrders = collect($bookings)->where('status', 'in_progress')->count();
            $cancelledOrders = collect($bookings)->where('status', 'cancelled')->count();
            $totalSpent = collect($bookings)->sum('total');
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
                <h1 class="text-5xl font-black text-white mb-3">Order History</h1>
                <p class="text-xl text-white/80">Track all your laundry orders and view past bookings</p>
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

            <!-- Completed Orders -->
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

            <!-- In Progress Orders -->
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

            <!-- Total Spent -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-warning transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-warning/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-warning/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Total Spent</p>
                    <p class="text-3xl font-black text-gray-900">₱{{ number_format($totalSpent, 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        @php
            $isCustom = request('custom') == '1' || (request('start_date') && request('end_date'));
            $currentPeriod = $isCustom ? 'custom' : (request('period') ?? 'all');
        @endphp
        <x-modules.filter-panel
            :action="route('user.history')"
            :status-filters="[
                ['key' => 'all', 'label' => 'All Time', 'color' => 'primary', 'icon' => 'list'],
                ['key' => 'today', 'label' => 'Today', 'color' => 'blue'],
                ['key' => 'week', 'label' => 'This Week', 'color' => 'green'],
                ['key' => 'month', 'label' => 'This Month', 'color' => 'yellow'],
                ['key' => 'year', 'label' => 'This Year', 'color' => 'purple'],
                ['key' => 'custom', 'label' => 'Custom Range', 'color' => 'red'],
            ]"
            :current-status="$currentPeriod"
            :show-search="true"
            search-id="search-input"
            search-placeholder="Search by order ID, service name, or branch..."
            :client-side="true"
            :show-date-range="true"
            :show-custom-date-filter="true"
            :start-date-value="$startDate"
            :end-date-value="$endDate"
            start-date-label="From Date"
            end-date-label="To Date"
            :clear-url="route('user.history')"
            :show-clear="request()->hasAny(['period', 'start_date', 'end_date', 'custom'])"
            submit-text="Apply Filter"
            grid-cols="lg:grid-cols-3"
        />

        @push('scripts')
        <script>
            document.querySelectorAll('.filter-btn[data-filter]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    let url = '{{ route("user.history") }}';
                    
                    if (filter === 'custom') {
                        url += '?custom=1';
                    } else if (filter === 'all') {
                        // No parameters for all time
                    } else {
                        url += `?period=${filter}`;
                    }
                    
                    window.location.href = url;
                });
            });
        </script>
        @endpush
        />

        <!-- Order List -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-black text-gray-900">Recent Orders</h2>
            </div>

            <div id="bookings-container" class="space-y-4">
                @forelse($bookings as $index => $booking)
                    <div class="booking-card group bg-white rounded-xl border-2 border-gray-200 hover:border-wash hover:shadow-lg transition-all overflow-hidden" 
                         data-status="{{ $booking['status'] }}"
                         data-order-id="{{ $booking['id'] ?? $index + 1 }}"
                         data-services="{{ collect($booking['services'])->pluck('name')->implode(' ') }}">
                        
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
                                                        <span class="ml-2 text-wash/70">₱{{ $service['price'] }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Price & Actions -->
                                <div class="flex lg:flex-col items-center lg:items-end justify-between lg:justify-start gap-4 pt-4 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                                    <div class="text-right">
                                        <p class="text-xs text-gray-600 font-bold uppercase tracking-wide mb-1">Total</p>
                                        <p class="text-3xl font-black text-gray-900">₱{{ number_format($booking['total'], 2) }}</p>
                                    </div>
                                    
                                    @if($booking['status'] === 'completed')
                                    <button type="button" 
                                            onclick="openReceiptModal({{ $booking['id'] }})"
                                            class="btn btn-secondary">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div id="empty-state" class="rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-2">No Orders Yet</h3>
                        <p class="text-gray-500 mb-6">Start your first laundry order today!</p>
                        <a href="{{ route('user.booking') }}" class="btn btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Book Now
                        </a>
                    </div>
                @endforelse

                <!-- No Results -->
                <div id="no-results" class="hidden rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">No Results Found</h3>
                    <p class="text-gray-500">Try adjusting your search or filter</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receipt-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeReceiptModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all border-2 border-gray-200">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b-2 border-gray-200">
                    <h3 class="text-xl font-black text-gray-900">Order Receipt</h3>
                    <button type="button" onclick="closeReceiptModal()" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div id="receipt-content" class="p-6 max-h-[70vh] overflow-y-auto">
                    <div class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-wash"></div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex gap-3 p-6 border-t-2 border-gray-200">
                    <button type="button" onclick="closeReceiptModal()" class="flex-1 btn btn-secondary">Close</button>
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
                'completed': 'badge badge-completed',
                'in_progress': 'badge badge-in-progress',
                'cancelled': 'badge badge-cancelled',
                'pending': 'badge badge-pending'
            };
            const statusClass = statusClasses[booking.status] || 'badge badge-pending';
            const statusText = booking.status.replace('_', ' ');

            let servicesHTML = '';
            if (booking.services && booking.services.length > 0) {
                servicesHTML = `
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Services</label>
                        <div class="bg-gray-50 rounded-xl p-4 space-y-2 border-2 border-gray-200">
                            ${booking.services.map(s => `
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-900 font-medium">${s.name}</span>
                                    <span class="font-bold text-gray-900">₱${parseFloat(s.price).toFixed(2)}</span>
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
                        <label class="block text-sm font-bold text-gray-700 mb-2">Products</label>
                        <div class="bg-gray-50 rounded-xl p-4 space-y-2 border-2 border-gray-200">
                            ${booking.products.map(p => `
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-900 font-medium">${p.name}</span>
                                    <span class="font-bold text-gray-900">₱${parseFloat(p.price).toFixed(2)}</span>
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
                            <label class="block text-sm font-bold text-gray-700 mb-1">Order ID</label>
                            <span class="text-lg font-black text-gray-900">#${String(booking.id).padStart(6, '0')}</span>
                        </div>
                        <span class="${statusClass}">
                            ${statusText}
                        </span>
                    </div>

                    <!-- Booking Details -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Booking Details</label>
                        <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm border-2 border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-gray-600 font-medium">Date</span>
                                <span class="font-bold text-gray-900">${booking.date}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 font-medium">Time</span>
                                <span class="font-bold text-gray-900">${booking.time}</span>
                            </div>
                            ${booking.service_description ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600 font-medium">Service Type</span>
                                <span class="font-bold text-gray-900">${booking.service_description}</span>
                            </div>
                            ` : ''}
                            ${booking.branch_name ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600 font-medium">Branch</span>
                                <span class="font-bold text-gray-900">${booking.branch_name}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                    ${servicesHTML}
                    ${productsHTML}

                    <!-- Total -->
                    <div class="bg-gradient-to-br from-wash/10 to-wash/5 rounded-xl p-4 border-2 border-wash/20">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-gray-700 uppercase">Total Amount</span>
                            <span class="text-3xl font-black text-wash">₱${parseFloat(booking.total || 0).toFixed(2)}</span>
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
