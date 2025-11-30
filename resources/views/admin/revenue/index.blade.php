<x-layout>
    <x-slot name="title">Revenue Report</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="card p-6 print:shadow-none">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Revenue Report</h1>
                    <p class="text-gray-600 mt-1">{{ $start->format('M d, Y') }} - {{ $end->format('M d, Y') }}</p>
                </div>
                <button onclick="window.print()" class="btn btn-primary print:hidden">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Report
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="card p-6 print:hidden">
            <form method="GET" action="{{ route('admin.revenue.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="form-group">
                    <label class="form-label">Period</label>
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-input" value="{{ request('start_date') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-input" value="{{ request('end_date') }}">
                </div>
                <div class="form-group flex items-end">
                    <button type="submit" class="btn btn-primary w-full">Apply Filter</button>
                </div>
            </form>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card p-6">
                <p class="text-sm text-gray-600">Total Revenue</p>
                <p class="text-3xl font-bold text-success mt-2">₱{{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div class="card p-6">
                <p class="text-sm text-gray-600">Total Transactions</p>
                <p class="text-3xl font-bold text-primary mt-2">{{ number_format($totalTransactions) }}</p>
            </div>
            <div class="card p-6">
                <p class="text-sm text-gray-600">Average Order</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">₱{{ number_format($averageOrder, 2) }}</p>
            </div>
        </div>

        <!-- Revenue by Service -->
        <div class="card p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Revenue by Service</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($revenueByService as $service => $data)
                    <div class="border rounded-lg p-4">
                        <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $service)) }}</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">₱{{ number_format($data['revenue'], 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $data['count'] }} booking(s)</p>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-3">No revenue data available</p>
                @endforelse
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-900">Transaction Details</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Transaction ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">{{ $transaction->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm font-medium">#{{ $transaction->id }}</td>
                                <td class="px-6 py-4 text-sm">{{ $transaction->user->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm">{{ ucfirst(str_replace('_', ' ', $transaction->service_type)) }}</td>
                                <td class="px-6 py-4">
                                    <span class="badge badge-{{ $transaction->status === 'completed' ? 'success' : 'primary' }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-right">₱{{ number_format($transaction->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">No transactions found for this period</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($transactions->count() > 0)
                        <tfoot class="bg-gray-50 font-semibold">
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-right">Total:</td>
                                <td class="px-6 py-4 text-right">₱{{ number_format($totalRevenue, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            body { background: white; }
            .sidebar, .navbar, .print\:hidden { display: none !important; }
            .card { box-shadow: none; border: 1px solid #e5e7eb; }
            main { margin: 0 !important; padding: 20px !important; }
        }
    </style>
    @endpush
</x-layout>
