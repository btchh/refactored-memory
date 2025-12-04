<x-layout>
    <x-slot name="title">User Details - {{ $user->fname }} {{ $user->lname }}</x-slot>

    <div class="space-y-6">
        <!-- Back Button -->
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Users
        </a>

        <!-- User Info Card -->
        <div class="card p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                        <span class="text-2xl font-bold text-primary-600">{{ strtoupper(substr($user->fname, 0, 1)) }}{{ strtoupper(substr($user->lname, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->fname }} {{ $user->lname }}</h1>
                        <p class="text-gray-600">@{{ $user->username }}</p>
                        <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-error' }} mt-2">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="toggleUserStatus({{ $user->id }}, '{{ $user->status }}')" class="btn btn-outline">
                        {{ $user->status === 'active' ? 'Disable Account' : 'Enable Account' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Contact & Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Contact Information -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Contact Information</h2>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Email</label>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Phone</label>
                        <p class="text-gray-900">{{ $user->phone }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Address</label>
                        <p class="text-gray-900">{{ $user->address }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Member Since</label>
                        <p class="text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Booking Statistics -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Booking Statistics</h2>
                </div>
                <div class="card-body space-y-4">
                    <div class="border-b border-gray-200 pb-3 mb-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">All Branches</p>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600">Total Bookings</span>
                                <span class="text-lg font-bold text-gray-900">{{ $bookingStats['total'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600">Total Spent</span>
                                <span class="text-lg font-bold text-gray-900">₱{{ number_format($bookingStats['total_spent'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Completed</span>
                        <span class="text-lg font-bold text-success">{{ $bookingStats['completed'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Pending</span>
                        <span class="text-lg font-bold text-warning">{{ $bookingStats['pending'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Cancelled</span>
                        <span class="text-lg font-bold text-error">{{ $bookingStats['cancelled'] }}</span>
                    </div>
                    @if($bookingStats['branch_bookings'] > 0)
                        <div class="border-t border-gray-200 pt-3 mt-3">
                            <p class="text-xs font-semibold text-primary-600 uppercase mb-2">Your Branch</p>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600">Bookings</span>
                                    <span class="text-lg font-bold text-primary-600">{{ $bookingStats['branch_bookings'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600">Revenue</span>
                                    <span class="text-lg font-bold text-primary-600">₱{{ number_format($bookingStats['branch_spent'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recent Bookings (All Branches)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Branch</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($user->transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->booking_date->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($transaction->booking_time)->format('g:i A') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($transaction->admin)
                                        <p class="text-sm text-gray-900">{{ $transaction->admin->admin_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $transaction->admin->branch_address ?? 'N/A' }}</p>
                                    @else
                                        <span class="text-xs text-gray-400">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-900">{{ ucfirst($transaction->item_type) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900">₱{{ number_format($transaction->total_price, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge badge-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'error') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No bookings yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.userRoutes = {
            toggleStatus: '{{ route('admin.users.toggle-status', ['id' => '__ID__']) }}',
            csrf: '{{ csrf_token() }}'
        };

        function toggleUserStatus(userId, currentStatus) {
            const action = currentStatus === 'active' ? 'disable' : 'enable';
            if (!confirm(`Are you sure you want to ${action} this user?`)) return;

            fetch(window.userRoutes.toggleStatus.replace('__ID__', userId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.userRoutes.csrf,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update user status');
            });
        }
    </script>
    @endpush
</x-layout>
