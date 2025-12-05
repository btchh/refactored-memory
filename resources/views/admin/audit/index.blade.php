<x-layout>
    <x-slot name="title">Audit Log</x-slot>

    @php
        // Get current admin
        $adminId = Auth::guard('admin')->id();
        
        // Get counts for each action type (from database, not paginated collection)
        $baseQuery = \App\Models\AuditLog::forAdmin($adminId);
        $allTimeTotal = $baseQuery->count();
        $createdCount = (clone $baseQuery)->where('action', 'created')->count();
        $updatedCount = (clone $baseQuery)->where('action', 'updated')->count();
        $statusChangedCount = (clone $baseQuery)->where('action', 'status_changed')->count();
        $loginCount = (clone $baseQuery)->where('action', 'login')->count();
        $deletedCount = (clone $baseQuery)->where('action', 'deleted')->count();
    @endphp

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
                <h1 class="text-5xl font-black text-white mb-3">Audit Log</h1>
                <p class="text-xl text-white/80">Track all actions performed in your branch</p>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <!-- Created Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-success transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-success/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Created</p>
                    <p class="text-3xl font-black text-gray-900">{{ $createdCount }}</p>
                </div>
            </div>

            <!-- Updated Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-info transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-info/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-info/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Updated</p>
                    <p class="text-3xl font-black text-gray-900">{{ $updatedCount }}</p>
                </div>
            </div>

            <!-- Status Changed Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-warning transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-warning/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-warning/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Status Changed</p>
                    <p class="text-3xl font-black text-gray-900">{{ $statusChangedCount }}</p>
                </div>
            </div>

            <!-- Logins Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-wash transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-wash/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-wash/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Logins</p>
                    <p class="text-3xl font-black text-gray-900">{{ $loginCount }}</p>
                </div>
            </div>

            <!-- Deleted Stat Card -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-error transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-error/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-error/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Deleted</p>
                    <p class="text-3xl font-black text-gray-900">{{ $deletedCount }}</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-black text-gray-900">Filter Logs</h2>
            </div>
            
            <div class="space-y-4">
                <!-- Search Input -->
                <div class="form-group">
                    <label for="search" class="form-label">Search Description</label>
                    <input 
                        type="text" 
                        id="search"
                        name="search" 
                        class="form-input" 
                        placeholder="Search description..."
                        value="{{ request('search') }}"
                    >
                </div>

                <!-- Action Filter Buttons -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <button class="filter-btn {{ request('action') === null ? 'bg-wash text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-4 py-2 rounded-xl font-bold text-sm transition-all" data-filter="">
                        All ({{ $allTimeTotal }})
                    </button>
                    <button class="filter-btn {{ request('action') === 'created' ? 'bg-success text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-4 py-2 rounded-xl font-bold text-sm transition-all" data-filter="created">
                        Created ({{ $createdCount }})
                    </button>
                    <button class="filter-btn {{ request('action') === 'updated' ? 'bg-info text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-4 py-2 rounded-xl font-bold text-sm transition-all" data-filter="updated">
                        Updated ({{ $updatedCount }})
                    </button>
                    <button class="filter-btn {{ request('action') === 'status_changed' ? 'bg-warning text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-4 py-2 rounded-xl font-bold text-sm transition-all" data-filter="status_changed">
                        Status ({{ $statusChangedCount }})
                    </button>
                    <button class="filter-btn {{ request('action') === 'login' ? 'bg-wash text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-4 py-2 rounded-xl font-bold text-sm transition-all" data-filter="login">
                        Logins ({{ $loginCount }})
                    </button>
                    <button class="filter-btn {{ request('action') === 'deleted' ? 'bg-error text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} px-4 py-2 rounded-xl font-bold text-sm transition-all" data-filter="deleted">
                        Deleted ({{ $deletedCount }})
                    </button>
                </div>

                @if(request('action') || request('search'))
                    <div class="flex justify-end">
                        <a href="{{ route('admin.audit') }}" class="text-sm text-wash hover:text-wash-dark font-bold transition-colors">
                            Clear Filters →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Audit Log Table - Desktop View -->
        <div class="hidden lg:block bg-white rounded-2xl border-2 border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b-2 border-gray-200">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Model</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            @php
                                $actionConfig = [
                                    'created' => ['badge' => 'badge-completed', 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
                                    'updated' => ['badge' => 'badge-in-progress', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                                    'deleted' => ['badge' => 'badge-cancelled', 'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
                                    'status_changed' => ['badge' => 'badge-pending', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                                    'login' => ['badge' => 'badge-in-progress', 'icon' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1'],
                                    'logout' => ['badge' => 'inline-block text-xs px-3 py-1 rounded-full font-bold uppercase bg-gray-100 text-gray-700', 'icon' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'],
                                ];
                                $config = $actionConfig[$log->action] ?? ['badge' => 'inline-block text-xs px-3 py-1 rounded-full font-bold uppercase bg-gray-100 text-gray-700', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'];
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $log->created_at->format('M j, Y') }}</p>
                                            <p class="text-xs text-gray-500">{{ $log->created_at->format('g:i A') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="badge {{ $config['badge'] }} inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                                        </svg>
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900 break-words max-w-md">
                                        {{ $log->description }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->model_type)
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-xl text-xs font-bold">
                                                {{ class_basename($log->model_type) }}
                                            </span>
                                            @if($log->model_id)
                                                <span class="text-xs text-gray-400 font-medium">#{{ $log->model_id }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-wash/10 flex items-center justify-center">
                                            <span class="text-xs font-bold text-wash">
                                                {{ strtoupper(substr($log->admin->fname, 0, 1)) }}{{ strtoupper(substr($log->admin->lname, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $log->admin->fname }} {{ $log->admin->lname }}</span>
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
                                        <p class="text-gray-900 font-black text-lg mb-1">No audit logs found</p>
                                        <p class="text-gray-500 text-sm">Try adjusting your filters or date range</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t-2 border-gray-200 bg-gray-50/50">
                    {{ $logs->withQueryString()->links() }}
                </div>
            @endif
        </div>

        <!-- Audit Log Cards - Mobile View -->
        <div class="lg:hidden space-y-4">
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
                <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $log->created_at->format('M j, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $log->created_at->format('g:i A') }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['border'] }} rounded-lg text-xs font-semibold flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                            </svg>
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <p class="text-sm text-gray-900 break-words">
                            {{ $log->description }}
                        </p>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between gap-3 pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-semibold text-primary-700">
                                    {{ strtoupper(substr($log->admin->fname, 0, 1)) }}{{ strtoupper(substr($log->admin->lname, 0, 1)) }}
                                </span>
                            </div>
                            <span class="text-xs text-gray-700 truncate">{{ $log->admin->fname }} {{ $log->admin->lname }}</span>
                        </div>
                        @if($log->model_type)
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs font-medium">
                                    {{ class_basename($log->model_type) }}
                                </span>
                                @if($log->model_id)
                                    <span class="text-xs text-gray-400">#{{ $log->model_id }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-900 font-medium mb-1">No audit logs found</p>
                    <p class="text-gray-500 text-sm">Try adjusting your filters or date range</p>
                </div>
            @endforelse

            @if($logs->hasPages())
                <div class="mt-4">
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
                const search = document.querySelector('input[name="search"]')?.value || '';
                let url = '{{ route("admin.audit") }}';
                const params = new URLSearchParams();
                
                // Add action filter if not empty
                if (filter && filter !== '') {
                    params.set('action', filter);
                }
                
                // Preserve search
                if (search) {
                    params.set('search', search);
                }
                
                if (params.toString()) {
                    url += '?' + params.toString();
                }
                
                window.location.href = url;
            });
        });

        // Handle search on Enter key
        document.querySelector('input[name="search"]')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const search = this.value;
                const currentUrl = new URL(window.location.href);
                if (search) {
                    currentUrl.searchParams.set('search', search);
                } else {
                    currentUrl.searchParams.delete('search');
                }
                window.location.href = currentUrl.toString();
            }
        });
    </script>
    @endpush
</x-layout>
