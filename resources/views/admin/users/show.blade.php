<x-layout>
    <x-slot name="title">User Details - {{ $user->fname }} {{ $user->lname }}</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-wash font-bold transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Users
        </a>

        <!-- Hero Header -->
        <div class="relative bg-gradient-to-br from-wash via-wash-dark to-gray-900 rounded-2xl p-12 overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
            </div>
            
            <!-- Content -->
            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                        <span class="text-3xl font-black text-white">{{ strtoupper(substr($user->fname, 0, 1)) }}{{ strtoupper(substr($user->lname, 0, 1)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black text-white mb-1">{{ $user->fname }} {{ $user->lname }}</h1>
                        <p class="text-lg text-white/80 mb-2">@{{ $user->username }}</p>
                        <span class="badge inline-block text-xs px-3 py-1 rounded-full font-bold uppercase {{ $user->status === 'active' ? 'bg-success/20 text-white' : 'bg-error/20 text-white' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="toggleUserStatus({{ $user->id }}, '{{ $user->status }}')" class="btn inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold transition-all {{ $user->status === 'active' ? 'bg-warning text-white hover:bg-warning/90' : 'bg-success text-white hover:bg-success/90' }} hover:scale-105 shadow-xl">
                        {{ $user->status === 'active' ? 'Disable Account' : 'Enable Account' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Contact & Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
                <h2 class="text-xl font-black text-gray-900 mb-5">Contact Information</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-bold text-gray-600">Email</label>
                        <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-bold text-gray-600">Phone</label>
                        <p class="text-gray-900 font-medium">{{ $user->phone }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-bold text-gray-600">Address</label>
                        <p class="text-gray-900 font-medium">{{ $user->address }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-bold text-gray-600">Member Since</label>
                        <p class="text-gray-900 font-medium">{{ $user->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Booking Statistics -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
                <h2 class="text-xl font-black text-gray-900 mb-5">Booking Statistics</h2>
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-3 mb-3">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-2">All Branches</p>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-600">Total Bookings</span>
                                <span class="text-lg font-black text-gray-900">{{ $bookingStats['total'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-600">Total Spent</span>
                                <span class="text-lg font-black text-gray-900">₱{{ number_format($bookingStats['total_spent'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-600">Completed</span>
                        <span class="text-lg font-black text-success">{{ $bookingStats['completed'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-600">Pending</span>
                        <span class="text-lg font-black text-warning">{{ $bookingStats['pending'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-600">Cancelled</span>
                        <span class="text-lg font-black text-error">{{ $bookingStats['cancelled'] }}</span>
                    </div>
                    @if($bookingStats['branch_bookings'] > 0)
                        <div class="border-t border-gray-200 pt-3 mt-3">
                            <p class="text-xs font-bold text-wash uppercase mb-2">Your Branch</p>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-bold text-gray-600">Bookings</span>
                                    <span class="text-lg font-black text-wash">{{ $bookingStats['branch_bookings'] }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-bold text-gray-600">Revenue</span>
                                    <span class="text-lg font-black text-wash">₱{{ number_format($bookingStats['branch_spent'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
            <h2 class="text-xl font-black text-gray-900 mb-5">Recent Bookings (All Branches)</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Branch</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Item Type</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($user->transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-gray-900">{{ $transaction->booking_date->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500 font-medium">{{ \Carbon\Carbon::parse($transaction->booking_time)->format('g:i A') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($transaction->admin)
                                        <p class="text-sm font-bold text-gray-900">{{ $transaction->admin->branch_name }}</p>
                                        <p class="text-xs text-gray-500 font-medium">{{ $transaction->admin->branch_address ?? 'N/A' }}</p>
                                    @else
                                        <span class="text-xs text-gray-400 font-medium">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-900">{{ ucfirst($transaction->item_type) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-black text-gray-900">₱{{ number_format($transaction->total_price, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge inline-block text-xs px-3 py-1 rounded-full font-bold uppercase {{ $transaction->status === 'completed' ? 'bg-success/10 text-success' : ($transaction->status === 'pending' ? 'bg-warning/10 text-warning' : 'bg-error/10 text-error') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <p class="text-gray-900 font-bold mb-1">No bookings yet</p>
                                        <p class="text-gray-500 text-sm font-medium">This user hasn't made any bookings</p>
                                    </div>
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
