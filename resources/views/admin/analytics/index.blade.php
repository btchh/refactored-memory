<x-layout>
    <x-slot name="title">Analytics</x-slot>

    <div class="space-y-6">
        <!-- Header with Filters -->
        <div class="card p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">Analytics</h1>
                    <p class="text-gray-600">Business insights and performance metrics</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button type="button" onclick="setPeriod('day')" class="px-3 py-1.5 text-sm font-medium rounded-md {{ $period === 'day' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">Day</button>
                        <button type="button" onclick="setPeriod('week')" class="px-3 py-1.5 text-sm font-medium rounded-md {{ $period === 'week' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">Week</button>
                        <button type="button" onclick="setPeriod('month')" class="px-3 py-1.5 text-sm font-medium rounded-md {{ $period === 'month' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">Month</button>
                        <button type="button" onclick="setPeriod('year')" class="px-3 py-1.5 text-sm font-medium rounded-md {{ $period === 'year' ? 'bg-white shadow text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">Year</button>
                    </div>
                    <input type="date" id="date-picker" value="{{ $date }}" onchange="updateDate(this.value)" class="form-input text-sm">
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $revenueChange = $metrics['previous_revenue'] > 0 
                    ? (($metrics['current_revenue'] - $metrics['previous_revenue']) / $metrics['previous_revenue']) * 100 
                    : 0;
                $completionRate = $metrics['period_bookings'] > 0 
                    ? ($metrics['completed_bookings'] / $metrics['period_bookings']) * 100 
                    : 0;
            @endphp
            
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Revenue</p>
                <p class="text-2xl font-bold text-gray-900">₱{{ number_format($metrics['current_revenue'], 2) }}</p>
                <p class="text-xs mt-1 {{ $revenueChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $revenueChange >= 0 ? '↑' : '↓' }} {{ abs(number_format($revenueChange, 1)) }}% vs last {{ $period }}
                </p>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Bookings</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['period_bookings']) }}</p>
                <p class="text-xs text-gray-500 mt-1">This {{ $period }}</p>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Completed</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['completed_bookings']) }}</p>
                <p class="text-xs text-green-600 mt-1">{{ number_format($completionRate, 0) }}% completion</p>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <p class="text-sm text-gray-500 mb-1">Pending</p>
                <p class="text-2xl font-bold text-gray-900">{{ $metrics['pending_bookings'] }}</p>
                <p class="text-xs text-yellow-600 mt-1">Needs attention</p>
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
