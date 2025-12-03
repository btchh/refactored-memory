<x-layout>
    <x-slot name="title">Audit Log</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <x-modules.card class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Audit Log</h1>
                    <p class="text-gray-600">Track all actions performed in your branch</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span class="font-medium">{{ $logs->total() }}</span> total records
                </div>
            </div>
        </x-modules.card>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $actionCounts = $logs->getCollection()->groupBy('action')->map->count();
            @endphp
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-xs text-green-600 font-medium">Created</p>
                <p class="text-2xl font-bold text-green-700">{{ $actionCounts->get('created', 0) }}</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-xs text-blue-600 font-medium">Updated</p>
                <p class="text-2xl font-bold text-blue-700">{{ $actionCounts->get('updated', 0) }}</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-xs text-yellow-600 font-medium">Status Changed</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $actionCounts->get('status_changed', 0) }}</p>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <p class="text-xs text-purple-600 font-medium">Logins</p>
                <p class="text-2xl font-bold text-purple-700">{{ $actionCounts->get('login', 0) }}</p>
            </div>
        </div>

        <!-- Filters -->
        <x-modules.filter-panel
            :quick-filters="[
                ['label' => 'Today', 'url' => route('admin.audit', ['from' => now()->format('Y-m-d'), 'to' => now()->format('Y-m-d')]), 'active' => request('from') == now()->format('Y-m-d') && request('to') == now()->format('Y-m-d')],
                ['label' => 'Last 7 days', 'url' => route('admin.audit', ['from' => now()->subDays(7)->format('Y-m-d'), 'to' => now()->format('Y-m-d')])],
                ['label' => 'Last 30 days', 'url' => route('admin.audit', ['from' => now()->subDays(30)->format('Y-m-d'), 'to' => now()->format('Y-m-d')])],
                ['label' => 'This month', 'url' => route('admin.audit', ['from' => now()->startOfMonth()->format('Y-m-d'), 'to' => now()->format('Y-m-d')])],
            ]"
            :show-search="true"
            search-placeholder="Search description..."
            :show-date-range="true"
            start-date-name="from"
            end-date-name="to"
            start-date-label="From"
            end-date-label="To"
            :clear-url="route('admin.audit')"
            grid-cols="lg:grid-cols-6"
        >
            <x-slot name="fields">
                <div class="form-group">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select w-full">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $action)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Model</label>
                    <select name="model" class="form-select w-full">
                        <option value="">All Models</option>
                        @foreach($models as $model)
                            <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>
                                {{ $model }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </x-slot>
        </x-modules.filter-panel>

        <!-- Audit Log Table -->
        <x-modules.card class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $log->created_at->format('M j, Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->created_at->format('g:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $actionColors = [
                                            'created' => 'bg-green-100 text-green-800',
                                            'updated' => 'bg-blue-100 text-blue-800',
                                            'deleted' => 'bg-red-100 text-red-800',
                                            'status_changed' => 'bg-yellow-100 text-yellow-800',
                                            'login' => 'bg-purple-100 text-purple-800',
                                            'logout' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $color = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-md truncate">
                                    {{ $log->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($log->model_type)
                                        <span class="text-gray-700">{{ class_basename($log->model_type) }}</span>
                                        @if($log->model_id)
                                            <span class="text-gray-400">#{{ $log->model_id }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->admin->fname }} {{ $log->admin->lname }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p>No audit logs found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->withQueryString()->links() }}
                </div>
            @endif
        </x-modules.card>
    </div>
</x-layout>
