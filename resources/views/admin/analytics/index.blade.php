<x-layout>
    <x-slot name="title">Analytics</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <x-modules.page-header
            title="Analytics"
            subtitle="Business insights and performance metrics"
            icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
            gradient="violet"
        />

        <!-- Filters -->
        @php
            $isCustom = $useCustomRange || request('custom') == '1';
            $currentPeriod = $allTime ? 'all_time' : ($isCustom ? 'custom' : $period);
        @endphp
        <x-modules.filter-panel
            :action="route('admin.analytics.index')"
            :status-filters="[
                ['key' => 'all_time', 'label' => 'All Time', 'color' => 'primary', 'icon' => 'list'],
                ['key' => 'day', 'label' => 'Today', 'color' => 'blue'],
                ['key' => 'week', 'label' => 'This Week', 'color' => 'green'],
                ['key' => 'month', 'label' => 'This Month', 'color' => 'yellow'],
                ['key' => 'year', 'label' => 'This Year', 'color' => 'purple'],
                ['key' => 'custom', 'label' => 'Custom Range', 'color' => 'red'],
            ]"
            :current-status="$currentPeriod"
            :show-date-range="true"
            :show-custom-date-filter="true"
            :start-date-value="$startDate"
            :end-date-value="$endDate"
            :clear-url="route('admin.analytics.index')"
            :show-clear="$useCustomRange || $allTime"
            submit-text="Apply"
            grid-cols="lg:grid-cols-4"
        />

        @push('scripts')
        <script>
            document.querySelectorAll('.filter-btn[data-filter]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    let url = '{{ route("admin.analytics.index") }}';
                    
                    if (filter === 'custom') {
                        url += '?custom=1';
                    } else if (filter === 'all_time') {
                        url += '?all_time=true';
                    } else {
                        url += `?period=${filter}`;
                    }
                    
                    window.location.href = url;
                });
            });
        </script>
        @endpush

        <!-- Key Metrics -->
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
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
            
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Revenue</p>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">₱{{ number_format($metrics['current_revenue'], 2) }}</p>
                @if(!$allTime && !$useCustomRange)
                <p class="text-xs {{ $revenueChange >= 0 ? 'text-green-600' : 'text-red-600' }} flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $revenueChange >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"/>
                    </svg>
                    {{ abs(number_format($revenueChange, 1)) }}% vs last {{ $period }}
                </p>
                @elseif($useCustomRange)
                <p class="text-xs text-gray-500">Custom range</p>
                @else
                <p class="text-xs text-gray-500">All time total</p>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Avg Order</p>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">₱{{ number_format($metrics['average_order_value'], 2) }}</p>
                <p class="text-xs text-gray-500">Per completed booking</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-violet-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($metrics['period_bookings']) }}</p>
                <p class="text-xs text-gray-500">{{ $useCustomRange ? 'Custom range' : ($allTime ? 'All time' : 'This ' . $period) }}</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($metrics['completed_bookings']) }}</p>
                <p class="text-xs text-green-600">{{ number_format($completionRate, 1) }}% completion rate</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ $metrics['pending_bookings'] }}</p>
                <p class="text-xs text-amber-600">Needs attention</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Cancelled</p>
                </div>
                <p class="text-2xl font-bold text-gray-900 mb-1">{{ $metrics['cancelled_bookings'] }}</p>
                <p class="text-xs text-rose-600">{{ number_format($cancellationRate, 1) }}% cancellation rate</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Revenue Trend</h2>
                    <div class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-semibold">
                        ₱{{ number_format($metrics['current_revenue'], 2) }}
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Booking Status Distribution</h2>
                    <div class="px-3 py-1 bg-violet-50 text-violet-700 rounded-lg text-xs font-semibold">
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
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Bookings by Item Type</h2>
                </div>
                <div class="h-64">
                    <canvas id="itemTypeChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Performance Metrics</h2>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Completion Rate</p>
                                <p class="text-xs text-gray-500">Successfully completed orders</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-green-600">{{ number_format($completionRate, 1) }}%</p>
                            <p class="text-xs text-gray-500">{{ $metrics['completed_bookings'] }}/{{ $metrics['period_bookings'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-rose-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Cancellation Rate</p>
                                <p class="text-xs text-gray-500">Orders cancelled</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-rose-600">{{ number_format($cancellationRate, 1) }}%</p>
                            <p class="text-xs text-gray-500">{{ $metrics['cancelled_bookings'] }}/{{ $metrics['period_bookings'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Average Order Value</p>
                                <p class="text-xs text-gray-500">Per completed booking</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-blue-600">₱{{ number_format($metrics['average_order_value'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Popular Services & Products -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-primary-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900">Top Services</h2>
                            <p class="text-xs text-gray-600">Most popular services</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @forelse($popularServices as $index => $service)
                        <div class="flex items-center justify-between py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white text-sm font-bold flex items-center justify-center shadow-lg">{{ $index + 1 }}</span>
                                    @if($index === 0)
                                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-yellow-900" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $service->service_name }}</p>
                                    <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        {{ $service->count }} bookings
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-emerald-600">₱{{ number_format($service->total_revenue, 2) }}</p>
                                <p class="text-xs text-gray-500">revenue</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">No service data available</p>
                            <p class="text-xs text-gray-400 mt-1">Data will appear once bookings are made</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-emerald-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900">Top Products</h2>
                            <p class="text-xs text-gray-600">Best selling products</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @forelse($popularProducts as $index => $product)
                        <div class="flex items-center justify-between py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white text-sm font-bold flex items-center justify-center shadow-lg">{{ $index + 1 }}</span>
                                    @if($index === 0)
                                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-yellow-900" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $product->product_name }}</p>
                                    <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                        {{ $product->count }} purchases
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-emerald-600">₱{{ number_format($product->total_revenue, 2) }}</p>
                                <p class="text-xs text-gray-500">revenue</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">No product data available</p>
                            <p class="text-xs text-gray-400 mt-1">Data will appear once products are purchased</p>
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
