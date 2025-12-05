<x-layout>
    <x-slot name="title">Analytics</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Hero Header -->
        <div class="relative bg-gradient-to-br from-wash via-wash-dark to-gray-900 rounded-2xl p-12 overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
            </div>
            
            <!-- Content -->
            <div class="relative">
                <h1 class="text-5xl font-black text-white mb-3">Analytics</h1>
                <p class="text-xl text-white/80">Business insights and performance metrics</p>
            </div>
        </div>

        <!-- Date Range Filters -->
        @php
            $isCustom = $useCustomRange || request('custom') == '1';
            $currentPeriod = $allTime ? 'all_time' : ($isCustom ? 'custom' : $period);
        @endphp
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
            <h2 class="text-xl font-black text-gray-900 mb-5">Date Range Filters</h2>
            <form action="{{ route('admin.analytics.index') }}" method="GET" class="space-y-4">
                <!-- Quick Filters -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <button type="button" class="filter-btn px-4 py-3 rounded-xl border-2 font-bold text-sm transition-all {{ $allTime ? 'bg-wash text-white border-wash' : 'bg-gray-50 text-gray-700 border-gray-200 hover:border-wash' }}" data-filter="all_time">
                        All Time
                    </button>
                    <button type="button" class="filter-btn px-4 py-3 rounded-xl border-2 font-bold text-sm transition-all {{ $period === 'day' && !$allTime && !$isCustom ? 'bg-wash text-white border-wash' : 'bg-gray-50 text-gray-700 border-gray-200 hover:border-wash' }}" data-filter="day">
                        Today
                    </button>
                    <button type="button" class="filter-btn px-4 py-3 rounded-xl border-2 font-bold text-sm transition-all {{ $period === 'week' && !$allTime && !$isCustom ? 'bg-wash text-white border-wash' : 'bg-gray-50 text-gray-700 border-gray-200 hover:border-wash' }}" data-filter="week">
                        This Week
                    </button>
                    <button type="button" class="filter-btn px-4 py-3 rounded-xl border-2 font-bold text-sm transition-all {{ $period === 'month' && !$allTime && !$isCustom ? 'bg-wash text-white border-wash' : 'bg-gray-50 text-gray-700 border-gray-200 hover:border-wash' }}" data-filter="month">
                        This Month
                    </button>
                    <button type="button" class="filter-btn px-4 py-3 rounded-xl border-2 font-bold text-sm transition-all {{ $period === 'year' && !$allTime && !$isCustom ? 'bg-wash text-white border-wash' : 'bg-gray-50 text-gray-700 border-gray-200 hover:border-wash' }}" data-filter="year">
                        This Year
                    </button>
                    <button type="button" class="filter-btn px-4 py-3 rounded-xl border-2 font-bold text-sm transition-all {{ $isCustom ? 'bg-wash text-white border-wash' : 'bg-gray-50 text-gray-700 border-gray-200 hover:border-wash' }}" data-filter="custom">
                        Custom
                    </button>
                </div>

                <!-- Custom Date Range -->
                <div id="customDateRange" class="{{ $isCustom ? '' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-input" value="{{ $startDate }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-input" value="{{ $endDate }}">
                    </div>
                </div>

                <!-- Apply Button -->
                <div id="applyButton" class="{{ $isCustom ? '' : 'hidden' }}">
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        @push('scripts')
        <script>
            document.querySelectorAll('.filter-btn[data-filter]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    
                    if (filter === 'custom') {
                        document.getElementById('customDateRange').classList.remove('hidden');
                        document.getElementById('applyButton').classList.remove('hidden');
                    } else {
                        let url = '{{ route("admin.analytics.index") }}';
                        
                        if (filter === 'all_time') {
                            url += '?all_time=true';
                        } else {
                            url += `?period=${filter}`;
                        }
                        
                        window.location.href = url;
                    }
                });
            });
        </script>
        @endpush

        <!-- Key Metrics Stat Cards -->
        @php
            $revenueChange = $metrics['previous_revenue'] > 0 
                ? (($metrics['current_revenue'] - $metrics['previous_revenue']) / $metrics['previous_revenue']) * 100 
                : 0;
            $completionRate = $metrics['period_bookings'] > 0 
                ? ($metrics['completed_bookings'] / $metrics['period_bookings']) * 100 
                : 0;
            $cancellationRate = $metrics['period_bookings'] > 0 
                ? ($metrics['cancelled_bookings'] / $metrics['period_bookings']) * 100 
                : 0;
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Revenue Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-success transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-success/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Revenue</p>
                    <p class="text-3xl font-black text-gray-900">₱{{ number_format($metrics['current_revenue'], 2) }}</p>
                </div>
            </div>

            <!-- Average Order Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-info transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-info/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-info/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Avg Order</p>
                    <p class="text-3xl font-black text-gray-900">₱{{ number_format($metrics['average_order_value'], 2) }}</p>
                </div>
            </div>

            <!-- Total Bookings Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-wash transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-wash/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-wash/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Total Bookings</p>
                    <p class="text-3xl font-black text-gray-900">{{ number_format($metrics['period_bookings']) }}</p>
                </div>
            </div>

            <!-- Completed Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-success transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-success/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Completed</p>
                    <p class="text-3xl font-black text-gray-900">{{ number_format($metrics['completed_bookings']) }}</p>
                </div>
            </div>

            <!-- Pending Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-warning transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-warning/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-warning/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Pending</p>
                    <p class="text-3xl font-black text-gray-900">{{ $metrics['pending_bookings'] }}</p>
                </div>
            </div>

            <!-- Cancelled Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-error transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-error/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-error/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Cancelled</p>
                    <p class="text-3xl font-black text-gray-900">{{ $metrics['cancelled_bookings'] }}</p>
                </div>
            </div>
        </div>

        <!-- Charts Content Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Revenue Trend Chart -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-black text-gray-900">Revenue Trend</h2>
                    <div class="px-3 py-1 bg-success/10 text-success rounded-lg text-xs font-bold">
                        ₱{{ number_format($metrics['current_revenue'], 2) }}
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Booking Status Distribution Chart -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-black text-gray-900">Booking Status Distribution</h2>
                    <div class="px-3 py-1 bg-wash/10 text-wash rounded-lg text-xs font-bold">
                        {{ number_format($metrics['period_bookings']) }} Total
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Additional Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Item Type Chart -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
                <h2 class="text-xl font-black text-gray-900 mb-5">Bookings by Item Type</h2>
                <div class="h-64">
                    <canvas id="itemTypeChart"></canvas>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
                <h2 class="text-xl font-black text-gray-900 mb-5">Performance Metrics</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-success/5 rounded-xl border-2 border-success/20">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">Completion Rate</p>
                                <p class="text-xs text-gray-600">Successfully completed orders</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-black text-success">{{ number_format($completionRate, 1) }}%</p>
                            <p class="text-xs text-gray-600">{{ $metrics['completed_bookings'] }}/{{ $metrics['period_bookings'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-error/5 rounded-xl border-2 border-error/20">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-error/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">Cancellation Rate</p>
                                <p class="text-xs text-gray-600">Orders cancelled</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-black text-error">{{ number_format($cancellationRate, 1) }}%</p>
                            <p class="text-xs text-gray-600">{{ $metrics['cancelled_bookings'] }}/{{ $metrics['period_bookings'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-info/5 rounded-xl border-2 border-info/20">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-info/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">Average Order Value</p>
                                <p class="text-xs text-gray-600">Per completed booking</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-black text-info">₱{{ number_format($metrics['average_order_value'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Popular Services & Products -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Services Content Card -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden">
                <div class="p-6 border-b-2 border-gray-200 bg-gradient-to-r from-wash/10 to-wash/5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-wash flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900">Top Services</h2>
                            <p class="text-sm text-gray-600">Most popular services</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @forelse($popularServices as $index => $service)
                        <div class="flex items-center justify-between py-4 {{ !$loop->last ? 'border-b-2 border-gray-100' : '' }}">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <span class="w-12 h-12 rounded-xl bg-gradient-to-br from-wash to-wash-dark text-white text-sm font-black flex items-center justify-center shadow-lg">{{ $index + 1 }}</span>
                                    @if($index === 0)
                                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-warning rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $service->service_name }}</p>
                                    <p class="text-xs text-gray-600 flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        {{ $service->count }} bookings
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-success">₱{{ number_format($service->total_revenue, 2) }}</p>
                                <p class="text-xs text-gray-600">revenue</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <p class="text-gray-600 font-bold">No service data available</p>
                            <p class="text-xs text-gray-500 mt-1">Data will appear once bookings are made</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Top Products Content Card -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden">
                <div class="p-6 border-b-2 border-gray-200 bg-gradient-to-r from-success/10 to-success/5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-success flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900">Top Products</h2>
                            <p class="text-sm text-gray-600">Best selling products</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @forelse($popularProducts as $index => $product)
                        <div class="flex items-center justify-between py-4 {{ !$loop->last ? 'border-b-2 border-gray-100' : '' }}">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <span class="w-12 h-12 rounded-xl bg-gradient-to-br from-success to-success/80 text-white text-sm font-black flex items-center justify-center shadow-lg">{{ $index + 1 }}</span>
                                    @if($index === 0)
                                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-warning rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $product->product_name }}</p>
                                    <p class="text-xs text-gray-600 flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        {{ $product->count }} purchases
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-success">₱{{ number_format($product->total_revenue, 2) }}</p>
                                <p class="text-xs text-gray-600">revenue</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <p class="text-gray-600 font-bold">No product data available</p>
                            <p class="text-xs text-gray-500 mt-1">Data will appear once products are purchased</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.analyticsData = {
            revenue: @json($revenueData),
            itemType: @json($itemTypeDistribution),
            status: @json($statusDistribution),
            period: '{{ $period }}',
            date: '{{ $date }}'
        };
    </script>
    @vite(['resources/js/pages/admin-analytics.js'])
    @endpush
</x-layout>
