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
                ['key' => 'day', 'label' => 'Today', 'color' => 'blue'],
                ['key' => 'week', 'label' => 'This Week', 'color' => 'green'],
                ['key' => 'month', 'label' => 'This Month', 'color' => 'yellow'],
                ['key' => 'year', 'label' => 'This Year', 'color' => 'purple'],
                ['key' => 'all_time', 'label' => 'All Time', 'color' => 'primary', 'icon' => 'list'],
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
            
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Revenue</p>
                <p class="text-2xl font-bold text-green-600">₱{{ number_format($metrics['current_revenue'], 2) }}</p>
                @if(!$allTime && !$useCustomRange)
                <p class="text-xs mt-1 {{ $revenueChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $revenueChange >= 0 ? '↑' : '↓' }} {{ abs(number_format($revenueChange, 1)) }}% vs last {{ $period }}
                </p>
                @elseif($useCustomRange)
                <p class="text-xs mt-1 text-gray-500">Custom range</p>
                @else
                <p class="text-xs mt-1 text-gray-500">All time total</p>
                @endif
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Avg Order Value</p>
                <p class="text-2xl font-bold text-gray-900">₱{{ number_format($metrics['average_order_value'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Per completed booking</p>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Total Bookings</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['period_bookings']) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $useCustomRange ? 'Custom range' : ($allTime ? 'All time' : 'This ' . $period) }}</p>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Completed</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($metrics['completed_bookings']) }}</p>
                <p class="text-xs text-green-600 mt-1">{{ number_format($completionRate, 0) }}% completion</p>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $metrics['pending_bookings'] }}</p>
                <p class="text-xs text-yellow-600 mt-1">Needs attention</p>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Cancelled</p>
                <p class="text-2xl font-bold text-red-600">{{ $metrics['cancelled_bookings'] }}</p>
                <p class="text-xs text-red-600 mt-1">{{ number_format($cancellationRate, 0) }}% cancellation</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Revenue Trend</h2>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Bookings by Item Type</h2>
                <div class="h-64">
                    <canvas id="itemTypeChart"></canvas>
                </div>
            </div>
        </div>


        <!-- Popular Services & Products -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="font-bold text-gray-900">Top Services</h2>
                </div>
                <div class="p-4">
                    @forelse($popularServices as $index => $service)
                        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-primary-100 text-primary-600 text-xs font-bold flex items-center justify-center">{{ $index + 1 }}</span>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $service->service_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $service->count }} bookings</p>
                                </div>
                            </div>
                            <span class="font-semibold text-green-600">₱{{ number_format($service->total_revenue, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">No data available</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="font-bold text-gray-900">Top Products</h2>
                </div>
                <div class="p-4">
                    @forelse($popularProducts as $index => $product)
                        <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-green-100 text-green-600 text-xs font-bold flex items-center justify-center">{{ $index + 1 }}</span>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->product_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->count }} purchases</p>
                                </div>
                            </div>
                            <span class="font-semibold text-green-600">₱{{ number_format($product->total_revenue, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Booking Status</h2>
            <div class="h-64">
                <canvas id="statusChart"></canvas>
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
