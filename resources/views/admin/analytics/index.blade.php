<x-layout>
    <x-slot name="title">Analytics</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="card p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
                        <p class="text-gray-600 mt-1">Business insights and performance metrics</p>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex gap-2">
                        <button type="button" onclick="setPeriod('day')" 
                                class="period-btn {{ $period === 'day' ? 'active' : '' }} btn btn-sm btn-outline">
                            Day
                        </button>
                        <button type="button" onclick="setPeriod('week')" 
                                class="period-btn {{ $period === 'week' ? 'active' : '' }} btn btn-sm btn-outline">
                            Week
                        </button>
                        <button type="button" onclick="setPeriod('month')" 
                                class="period-btn {{ $period === 'month' ? 'active' : '' }} btn btn-sm btn-outline">
                            Month
                        </button>
                        <button type="button" onclick="setPeriod('year')" 
                                class="period-btn {{ $period === 'year' ? 'active' : '' }} btn btn-sm btn-outline">
                            Year
                        </button>
                    </div>
                    <div class="relative">
                        <input type="date" 
                               id="date-picker" 
                               value="{{ $date }}" 
                               onchange="updateDate(this.value)"
                               class="date-picker-input form-input text-sm px-3 py-2 pr-10 cursor-pointer">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Period Revenue -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Period Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">₱{{ number_format($metrics['current_revenue'], 2) }}</p>
                        @php
                            $change = $metrics['previous_revenue'] > 0 
                                ? (($metrics['current_revenue'] - $metrics['previous_revenue']) / $metrics['previous_revenue']) * 100 
                                : 0;
                        @endphp
                        <p class="text-xs mt-1 {{ $change >= 0 ? 'text-success' : 'text-error' }}">
                            {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}% vs previous {{ $period }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-success/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Period Bookings -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Period Bookings</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($metrics['period_bookings']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $metrics['completed_bookings'] }} completed</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed Bookings -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($metrics['completed_bookings']) }}</p>
                        @php
                            $completionRate = $metrics['period_bookings'] > 0 
                                ? ($metrics['completed_bookings'] / $metrics['period_bookings']) * 100 
                                : 0;
                        @endphp
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($completionRate, 1) }}% completion rate</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-success/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Bookings -->
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ $metrics['pending_bookings'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Awaiting action</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-warning/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Revenue Chart -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Revenue Trend 
                    <span class="text-sm font-normal text-gray-500">
                        ({{ ucfirst($period) }}ly breakdown)
                    </span>
                </h2>
                <canvas id="revenueChart" class="w-full" style="max-height: 300px;"></canvas>
            </div>

            <!-- Item Type Distribution -->
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Bookings by Item Type</h2>
                <canvas id="itemTypeChart" class="w-full" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Popular Services & Products -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Popular Services -->
            <div class="card">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Most Popular Services</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($popularServices as $index => $service)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary-50 flex items-center justify-center">
                                        <span class="text-sm font-semibold text-primary-600">{{ $index + 1 }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $service->service_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $service->count }} bookings</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">₱{{ number_format($service->total_revenue, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-8">No service data available</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Popular Products -->
            <div class="card">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Most Popular Products</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($popularProducts as $index => $product)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-success/10 flex items-center justify-center">
                                        <span class="text-sm font-semibold text-success">{{ $index + 1 }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $product->product_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $product->count }} purchases</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900">₱{{ number_format($product->total_revenue, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-8">No product data available</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="card p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Booking Status Distribution</h2>
            <canvas id="statusChart" class="w-full" style="max-height: 300px;"></canvas>
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

        function setPeriod(period) {
            const url = new URL(window.location.href);
            url.searchParams.set('period', period);
            window.location.href = url.toString();
        }

        function updateDate(date) {
            const url = new URL(window.location.href);
            url.searchParams.set('date', date);
            window.location.href = url.toString();
        }
    </script>
    @vite(['resources/js/pages/admin-analytics.js'])
    @endpush
</x-layout>
