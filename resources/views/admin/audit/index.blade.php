<x-layout>
    <x-slot name="title">Audit Log</x-slot>

    @php
        $actionCounts = $logs->getCollection()->groupBy('action')->map->count();
        $filteredTotal = $logs->total();
        $createdCount = $actionCounts->get('created', 0);
        $updatedCount = $actionCounts->get('updated', 0);
        $statusChangedCount = $actionCounts->get('status_changed', 0);
        $loginCount = $actionCounts->get('login', 0);
        $deletedCount = $actionCounts->get('deleted', 0);
        
        // Get actual total count (without date filters) for "All Time" display
        $allTimeTotal = \App\Models\AuditLog::forAdmin(Auth::guard('admin')->id())->count();
        
        $hasDateFilter = request('from') || request('to');
        $isCustomRequest = request('custom') == '1';
        $isToday = request('from') == now()->format('Y-m-d') && request('to') == now()->format('Y-m-d');
        $is7Days = request('from') == now()->subDays(7)->format('Y-m-d') && request('to') == now()->format('Y-m-d');
        $is30Days = request('from') == now()->subDays(30)->format('Y-m-d') && request('to') == now()->format('Y-m-d');
        $isThisMonth = request('from') == now()->startOfMonth()->format('Y-m-d') && request('to') == now()->format('Y-m-d');
        
        // Determine current period - if dates don't match any preset, it's custom
        if ($isCustomRequest) {
            $currentPeriod = 'custom';
        } elseif (!$hasDateFilter) {
            $currentPeriod = 'all';
        } elseif ($isToday) {
            $currentPeriod = 'today';
        } elseif ($is7Days) {
            $currentPeriod = '7days';
        } elseif ($is30Days) {
            $currentPeriod = '30days';
        } elseif ($isThisMonth) {
            $currentPeriod = 'month';
        } else {
            $currentPeriod = 'custom';
        }
    @endphp

    <div class="space-y-6">
        <!-- Header -->
        <x-modules.page-header
            title="Audit Log"
            subtitle="Track all actions performed in your branch"
            icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            gradient="slate"
        >
            <x-slot name="stats">
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2">
                    <p class="text-white/70 text-xs">Total Records</p>
                    <p class="text-xl font-bold">{{ number_format($allTimeTotal) }}</p>
                </div>
            </x-slot>
        </x-modules.page-header>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Created</p>
                        <p class="text-xl font-bold text-gray-900">{{ $createdCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Updated</p>
                        <p class="text-xl font-bold text-gray-900">{{ $updatedCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Status Changed</p>
                        <p class="text-xl font-bold text-gray-900">{{ $statusChangedCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-violet-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Logins</p>
                        <p class="text-xl font-bold text-gray-900">{{ $loginCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Deleted</p>
                        <p class="text-xl font-bold text-gray-900">{{ $deletedCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <x-modules.filter-panel
            :status-filters="[
                ['key' => 'all', 'label' => 'All Time', 'count' => $allTimeTotal, 'color' => 'primary', 'icon' => 'list'],
                ['key' => 'today', 'label' => 'Today', 'color' => 'blue'],
                ['key' => '7days', 'label' => 'Last 7 Days', 'color' => 'green'],
                ['key' => '30days', 'label' => 'Last 30 Days', 'color' => 'yellow'],
                ['key' => 'month', 'label' => 'This Month', 'color' => 'purple'],
                ['key' => 'custom', 'label' => 'Custom Range', 'color' => 'red'],
            ]"
            :current-status="$currentPeriod"
            :show-search="true"
            search-placeholder="Search description..."
            :show-date-range="true"
            :show-custom-date-filter="true"
            start-date-name="from"
            end-date-name="to"
            start-date-label="From"
            end-date-label="To"
            :clear-url="route('admin.audit')"
            grid-cols="lg:grid-cols-4"
        />

        <!-- Audit Log Table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Model</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            @php
                                $actionConfig = [
                                    'created' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
                                    'updated' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                                    'deleted' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
                                    'status_changed' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                                    'login' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200', 'icon' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1'],
                                    'logout' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'icon' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'],
                                ];
                                $config = $actionConfig[$log->action] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'];
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $log->created_at->format('M j, Y') }}</p>
                                            <p class="text-xs text-gray-500">{{ $log->created_at->format('g:i A') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['border'] }} rounded-lg text-xs font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                                        </svg>
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900 max-w-xs truncate" title="{{ $log->description }}">
                                        {{ $log->description }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->model_type)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">
                                                {{ class_basename($log->model_type) }}
                                            </span>
                                            @if($log->model_id)
                                                <span class="text-xs text-gray-400">#{{ $log->model_id }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                            <span class="text-xs font-semibold text-primary-700">
                                                {{ strtoupper(substr($log->admin->fname, 0, 1)) }}{{ strtoupper(substr($log->admin->lname, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $log->admin->fname }} {{ $log->admin->lname }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-900 font-medium mb-1">No audit logs found</p>
                                        <p class="text-gray-500 text-sm">Try adjusting your filters or date range</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $logs->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('.filter-btn[data-filter]').forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.dataset.filter;
                const today = new Date().toISOString().split('T')[0];
                let url = '{{ route("admin.audit") }}';
                
                if (filter === 'custom') {
                    // Show the date range section - reload with custom param to show inputs
                    url += '?custom=1';
                } else if (filter === 'today') {
                    url += `?from=${today}&to=${today}`;
                } else if (filter === '7days') {
                    const from = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
                    url += `?from=${from}&to=${today}`;
                } else if (filter === '30days') {
                    const from = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
                    url += `?from=${from}&to=${today}`;
                } else if (filter === 'month') {
                    const date = new Date();
                    const from = new Date(date.getFullYear(), date.getMonth(), 1).toISOString().split('T')[0];
                    url += `?from=${from}&to=${today}`;
                }
                // 'all' goes to base URL without params
                
                window.location.href = url;
            });
        });
    </script>
    @endpush
</x-layout>
