<x-layout>
    <x-slot name="title">User Management</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <x-modules.page-header
            title="User Management"
            subtitle="Manage customer accounts and access"
            icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
            gradient="indigo"
        >
            <x-slot name="stats">
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2">
                    <p class="text-white/70 text-xs">Total Users</p>
                    <p class="text-xl font-bold">{{ number_format($stats['total']) }}</p>
                </div>
            </x-slot>
        </x-modules.page-header>

        <!-- Alert Container -->
        <div id="alert-container"></div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-modules.card class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </x-modules.card>

            <x-modules.card class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <p class="text-2xl font-bold text-success mt-2">{{ number_format($stats['active']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-success/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-modules.card>

            <x-modules.card class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Disabled Users</p>
                        <p class="text-2xl font-bold text-error mt-2">{{ number_format($stats['disabled']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-error/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                </div>
            </x-modules.card>
        </div>

        <!-- Filters & Search -->
        <x-modules.filter-panel
            :status-filters="[
                ['key' => '', 'label' => 'All', 'count' => $stats['total'], 'color' => 'primary', 'icon' => 'list'],
                ['key' => 'active', 'label' => 'Active', 'count' => $stats['active'], 'color' => 'green'],
                ['key' => 'disabled', 'label' => 'Disabled', 'count' => $stats['disabled'], 'color' => 'red'],
                ['key' => 'archived', 'label' => 'Archived', 'count' => $stats['archived'], 'color' => 'gray'],
            ]"
            :current-status="($showDeleted ?? false) ? 'archived' : ($status ?? '')"
            :show-search="true"
            search-placeholder="Search by name, email, username, or phone..."
            :clear-url="route('admin.users.index')"
            grid-cols="lg:grid-cols-4"
        />

        @push('scripts')
        <script>
            document.querySelectorAll('.filter-btn[data-filter]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    const search = document.querySelector('input[name="search"]')?.value || '';
                    let url = '{{ route("admin.users.index") }}';
                    const params = new URLSearchParams();
                    
                    if (filter === 'archived') {
                        params.set('deleted', 'true');
                    } else if (filter) {
                        params.set('status', filter);
                    }
                    
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

        <!-- Users Table -->
        <x-modules.card :padding="false">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bookings</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors" id="user-row-{{ $user->id }}" data-user-name="{{ $user->fname }} {{ $user->lname }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-sm">
                                            <span class="text-white font-bold text-sm">{{ strtoupper(substr($user->fname, 0, 1)) }}{{ strtoupper(substr($user->lname, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $user->fname }} {{ $user->lname }}</p>
                                            <p class="text-xs text-gray-500 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                {{ '@' . $user->username }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-900 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $user->email }}
                                        </p>
                                        <p class="text-xs text-gray-500 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            {{ $user->phone }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-bold text-gray-900">{{ $user->total_transactions_count }}</span>
                                        </div>
                                        @if($user->transactions_count > 0)
                                            <p class="text-xs text-gray-500 ml-10">{{ $user->transactions_count }} at your branch</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($showDeleted ?? false)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200" id="status-badge-{{ $user->id }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                            Archived
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}" id="status-badge-{{ $user->id }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $user->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($showDeleted ?? false)
                                            <button type="button" data-user-id="{{ $user->id }}" class="restore-user-btn inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors font-medium text-sm" title="Unarchive User">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Unarchive
                                            </button>
                                        @else
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors" title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <button type="button" data-user-id="{{ $user->id }}" data-user-status="{{ $user->status }}" class="toggle-status-btn inline-flex items-center justify-center w-9 h-9 rounded-lg {{ $user->status === 'active' ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} transition-colors" title="{{ $user->status === 'active' ? 'Disable' : 'Enable' }} User">
                                                @if($user->status === 'active')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @endif
                                            </button>
                                            <button type="button" data-user-id="{{ $user->id }}" class="delete-user-btn inline-flex items-center justify-center w-9 h-9 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="Archive User">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-900 font-medium mb-1">No users found</p>
                                        <p class="text-gray-500 text-sm">Try adjusting your search or filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $users->links() }}
                </div>
            @endif
        </x-modules.card>

        <!-- Toggle Status Modal -->
        <div id="toggle-status-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeToggleStatusModal()"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div id="toggle-icon-container" class="w-12 h-12 rounded-xl flex items-center justify-center">
                                <!-- Icon will be inserted by JS -->
                            </div>
                            <div>
                                <h3 id="toggle-modal-title" class="text-lg font-bold text-gray-900"></h3>
                                <p class="text-sm text-gray-500">Confirm your action</p>
                            </div>
                        </div>
                        <p id="toggle-modal-message" class="text-gray-600 mb-6"></p>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeToggleStatusModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                                Cancel
                            </button>
                            <button type="button" id="confirm-toggle-btn" class="flex-1 px-4 py-2.5 font-medium rounded-xl transition-colors">
                                Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Archive User Modal -->
        <div id="delete-user-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Archive User</h3>
                                <p class="text-sm text-gray-500">Can be restored anytime</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">
                            Are you sure you want to archive <strong id="delete-user-name" class="text-gray-900"></strong>? 
                            They will be moved to the archived users list and can be restored anytime.
                        </p>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                                Cancel
                            </button>
                            <button type="button" id="confirm-delete-btn" class="flex-1 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-xl transition-colors">
                                Archive User
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restore User Modal -->
        <div id="restore-user-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeRestoreModal()"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Restore User</h3>
                                <p class="text-sm text-gray-500">Unarchive account</p>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">
                            Are you sure you want to restore <strong id="restore-user-name" class="text-gray-900"></strong>? 
                            Their account will be unarchived and they'll be able to access the system again.
                        </p>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeRestoreModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                                Cancel
                            </button>
                            <button type="button" id="confirm-restore-btn" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl transition-colors">
                                Restore User
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.userRoutes = {
            toggleStatus: '{{ route('admin.users.toggle-status', ['id' => '__ID__']) }}',
            delete: '{{ route('admin.users.destroy', ['id' => '__ID__']) }}',
            restore: '{{ route('admin.users.restore', ['id' => '__ID__']) }}',
            csrf: '{{ csrf_token() }}'
        };
    </script>
    @vite(['resources/js/pages/admin-users.js'])
    @endpush
</x-layout>
