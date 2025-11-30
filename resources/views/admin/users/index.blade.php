<x-layout>
    <x-slot name="title">User Management</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <x-modules.card class="p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
                    <p class="text-gray-600 mt-1">Manage customer accounts and access</p>
                </div>
            </div>
        </x-modules.card>

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
        <x-modules.card class="p-6">
            <form method="GET" class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center">
                <div class="flex-1 min-w-0">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search by name, email, username, or phone..." 
                           class="form-input w-full">
                </div>
                <div class="flex gap-4 flex-shrink-0">
                    <select name="status" class="form-select w-full sm:w-48">
                        <option value="">All Status</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="disabled" {{ $status === 'disabled' ? 'selected' : '' }}>Disabled</option>
                    </select>
                    <x-modules.button type="submit" variant="primary">
                        <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span class="hidden sm:inline">Search</span>
                    </x-modules.button>
                    @if($search || $status)
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline whitespace-nowrap">Clear</a>
                    @endif
                </div>
            </form>
        </x-modules.card>

        <!-- Users Table -->
        <x-modules.card :padding="false">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50" id="user-row-{{ $user->id }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                                            <span class="text-primary-600 font-semibold">{{ strtoupper(substr($user->fname, 0, 1)) }}{{ strtoupper(substr($user->lname, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $user->fname }} {{ $user->lname }}</p>
                                            <p class="text-xs text-gray-500">@{{ $user->username }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ $user->email }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->phone }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900">{{ $user->transactions_count }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-error' }}" id="status-badge-{{ $user->id }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <button type="button" onclick="toggleUserStatus({{ $user->id }}, '{{ $user->status }}')" class="btn btn-sm btn-outline" title="{{ $user->status === 'active' ? 'Disable' : 'Enable' }} User">
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
                                    <button type="button" onclick="deleteUser({{ $user->id }}, '{{ $user->fname }} {{ $user->lname }}')" class="btn btn-sm btn-error" title="Delete User">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="text-sm font-medium">No users found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        window.userRoutes = {
            toggleStatus: '{{ route('admin.users.toggle-status', ['id' => '__ID__']) }}',
            delete: '{{ route('admin.users.destroy', ['id' => '__ID__']) }}',
            csrf: '{{ csrf_token() }}'
        };
    </script>
    @vite(['resources/js/pages/admin-users.js'])
    @endpush
</x-layout>
