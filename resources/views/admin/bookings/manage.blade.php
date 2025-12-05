<x-layout>
    <x-slot name="title">Booking Management</x-slot>

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
                <h1 class="text-5xl font-black text-white mb-3">Booking Management</h1>
                <p class="text-xl text-white/80">Update laundry booking status</p>
            </div>
        </div>

        <!-- Stat Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <!-- All Bookings -->
            <a href="{{ route('admin.bookings.manage', ['status' => 'all']) }}" 
               class="group relative bg-white rounded-2xl p-6 border-2 {{ $status === 'all' ? 'border-wash' : 'border-gray-200' }} hover:border-wash transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-wash/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-wash/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">All Bookings</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['all'] }}</p>
                </div>
            </a>

            <!-- Pending -->
            <a href="{{ route('admin.bookings.manage', ['status' => 'pending']) }}" 
               class="group relative bg-white rounded-2xl p-6 border-2 {{ $status === 'pending' ? 'border-warning' : 'border-gray-200' }} hover:border-warning transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-warning/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-warning/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Pending</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['pending'] }}</p>
                </div>
            </a>

            <!-- In Progress -->
            <a href="{{ route('admin.bookings.manage', ['status' => 'in_progress']) }}" 
               class="group relative bg-white rounded-2xl p-6 border-2 {{ $status === 'in_progress' ? 'border-info' : 'border-gray-200' }} hover:border-info transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-info/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-info/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">In Progress</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['in_progress'] }}</p>
                </div>
            </a>

            <!-- Completed -->
            <a href="{{ route('admin.bookings.manage', ['status' => 'completed']) }}" 
               class="group relative bg-white rounded-2xl p-6 border-2 {{ $status === 'completed' ? 'border-success' : 'border-gray-200' }} hover:border-success transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-success/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Completed</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['completed'] }}</p>
                </div>
            </a>

            <!-- Cancelled -->
            <a href="{{ route('admin.bookings.manage', ['status' => 'cancelled']) }}" 
               class="group relative bg-white rounded-2xl p-6 border-2 {{ $status === 'cancelled' ? 'border-error' : 'border-gray-200' }} hover:border-error transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-error/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-error/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Cancelled</p>
                    <p class="text-3xl font-black text-gray-900">{{ $stats['cancelled'] }}</p>
                </div>
            </a>
        </div>

        <!-- Search & Filters -->
        @php
            $hasDateFilter = $startDate || $endDate;
            $isCustomRequest = request('custom') == '1';
            $isToday = $startDate == now()->format('Y-m-d') && $endDate == now()->format('Y-m-d');
            $isTomorrow = $startDate == now()->addDay()->format('Y-m-d') && $endDate == now()->addDay()->format('Y-m-d');
            $isWeek = $startDate == now()->startOfWeek()->format('Y-m-d') && $endDate == now()->endOfWeek()->format('Y-m-d');
            $isMonth = $startDate == now()->startOfMonth()->format('Y-m-d') && $endDate == now()->endOfMonth()->format('Y-m-d');
            
            if ($isCustomRequest) {
                $currentDateFilter = 'custom';
            } elseif (!$hasDateFilter) {
                $currentDateFilter = 'all';
            } elseif ($isToday) {
                $currentDateFilter = 'today';
            } elseif ($isTomorrow) {
                $currentDateFilter = 'tomorrow';
            } elseif ($isWeek) {
                $currentDateFilter = 'week';
            } elseif ($isMonth) {
                $currentDateFilter = 'month';
            } else {
                $currentDateFilter = 'custom';
            }
        @endphp
        <x-modules.filter-panel
            :action="route('admin.bookings.manage')"
            :status-filters="[
                ['key' => 'all', 'label' => 'All Dates', 'color' => 'primary', 'icon' => 'list'],
                ['key' => 'today', 'label' => 'Today', 'color' => 'blue'],
                ['key' => 'tomorrow', 'label' => 'Tomorrow', 'color' => 'green'],
                ['key' => 'week', 'label' => 'This Week', 'color' => 'yellow'],
                ['key' => 'month', 'label' => 'This Month', 'color' => 'purple'],
                ['key' => 'custom', 'label' => 'Custom Range', 'color' => 'red'],
            ]"
            :current-status="$currentDateFilter"
            :show-search="true"
            search-placeholder="Search by customer name or email..."
            :search-value="$search"
            :show-date-range="true"
            :show-custom-date-filter="true"
            :start-date-value="$startDate"
            :end-date-value="$endDate"
            :clear-url="route('admin.bookings.manage', ['status' => $status])"
            :show-clear="$search || $startDate || $endDate"
            grid-cols="lg:grid-cols-4"
        >
            <x-slot name="hidden">
                <input type="hidden" name="status" value="{{ $status }}">
            </x-slot>
        </x-modules.filter-panel>

        @push('scripts')
        <script>
            document.querySelectorAll('.filter-btn[data-filter]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.dataset.filter;
                    const status = '{{ $status }}';
                    let url = '{{ route("admin.bookings.manage") }}?status=' + status;
                    
                    const today = new Date();
                    const formatDate = (d) => d.toISOString().split('T')[0];
                    
                    if (filter === 'custom') {
                        url += '&custom=1';
                    } else if (filter === 'today') {
                        url += `&start_date=${formatDate(today)}&end_date=${formatDate(today)}`;
                    } else if (filter === 'tomorrow') {
                        const tomorrow = new Date(today);
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        url += `&start_date=${formatDate(tomorrow)}&end_date=${formatDate(tomorrow)}`;
                    } else if (filter === 'week') {
                        const startOfWeek = new Date(today);
                        startOfWeek.setDate(today.getDate() - today.getDay());
                        const endOfWeek = new Date(startOfWeek);
                        endOfWeek.setDate(startOfWeek.getDate() + 6);
                        url += `&start_date=${formatDate(startOfWeek)}&end_date=${formatDate(endOfWeek)}`;
                    } else if (filter === 'month') {
                        const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                        const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        url += `&start_date=${formatDate(startOfMonth)}&end_date=${formatDate(endOfMonth)}`;
                    }
                    // 'all' goes without date params
                    
                    window.location.href = url;
                });
            });
        </script>
        @endpush

        <!-- Alert Container -->
        <div id="alert-container"></div>

        <!-- Bookings Table -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-black text-gray-900">Recent Bookings</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium">#{{ $booking->id }}</td>
                                <td class="px-6 py-4">
                                    @if($booking->user)
                                        <p class="font-medium text-gray-900">{{ $booking->user->fname }} {{ $booking->user->lname }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->user->email }}</p>
                                    @else
                                        <p class="font-medium text-gray-500">Archived User</p>
                                        <p class="text-xs text-gray-400">User no longer available</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <p>{{ $booking->formatted_date }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->formatted_time }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <p>{{ ucfirst($booking->item_type) }}</p>
                                    <p class="text-xs text-gray-500">₱{{ number_format($booking->total_price, 2) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <select onchange="updateStatus({{ $booking->id }}, this.value)" 
                                            class="form-select text-sm badge badge-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'in_progress' ? 'info' : ($booking->status === 'cancelled' ? 'error' : 'warning')) }}">
                                        <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $booking->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <button onclick="viewDetails({{ $booking->id }})" class="btn btn-sm btn-info" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        @if($booking->status !== 'completed' && $booking->status !== 'cancelled')
                                        <button onclick="openRescheduleModal({{ $booking->id }})" class="btn btn-sm btn-primary" title="Reschedule">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                        <button onclick="openWeightModal({{ $booking->id }}, '{{ $booking->weight ?? '' }}')" class="btn btn-sm btn-warning" title="Update Weight">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                            </svg>
                                        </button>
                                        <button onclick="cancelBooking({{ $booking->id }})" class="btn btn-sm btn-error" title="Cancel Booking">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">No bookings found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
                <div class="px-6 py-4 border-t">{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>

    <!-- Edit Weight Modal -->
    <dialog id="edit-weight-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-md w-full">
            <form method="dialog" class="absolute top-4 right-4">
                <button type="submit" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </form>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Update Weight</h3>
            
            <form id="edit-weight-form" class="space-y-4">
                <input type="hidden" id="edit-weight-booking-id">
                <div class="form-group">
                    <label class="form-label" for="edit-booking-weight">Weight (kg)</label>
                    <input type="number" id="edit-booking-weight" class="form-input" step="0.01" min="0" required>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('edit-weight-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Weight</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Details Modal -->
    <dialog id="details-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <form method="dialog" class="absolute top-4 right-4">
                <button type="submit" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </form>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Booking Details</h3>
            
            <div id="details-content" class="space-y-4">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </dialog>

    <!-- Reschedule Modal -->
    <dialog id="reschedule-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-md w-full">
            <form method="dialog" class="absolute top-4 right-4">
                <button type="submit" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </form>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Reschedule Booking</h3>
            
            <form id="reschedule-form" class="space-y-4">
                <input type="hidden" id="reschedule-booking-id">
                <div class="form-group">
                    <label class="form-label" for="reschedule-date">New Date</label>
                    <input type="date" id="reschedule-date" class="form-input" required min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="reschedule-time">New Time</label>
                    <select id="reschedule-time" class="form-select" required>
                        <option value="">Loading time slots...</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('reschedule-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reschedule</button>
                </div>
            </form>
        </div>
    </dialog>

    @push('scripts')
    <script>
        window.bookingRoutes = {
            updateStatus: '{{ route('admin.bookings.updateStatus', ['id' => '__ID__']) }}',
            updateWeight: '{{ route('admin.bookings.updateWeight', ['id' => '__ID__']) }}',
            details: '{{ route('admin.bookings.details', ['id' => '__ID__']) }}',
            reschedule: '{{ route('admin.bookings.reschedule', ['id' => '__ID__']) }}',
            cancel: '{{ route('admin.bookings.cancel', ['id' => '__ID__']) }}',
            slots: '{{ route('admin.api.calendar.slots') }}',
            csrf: '{{ csrf_token() }}'
        };

        function editWeightFromView(bookingId, currentWeight) {
            document.getElementById('details-modal').close();
            document.getElementById('edit-weight-booking-id').value = bookingId;
            document.getElementById('edit-booking-weight').value = currentWeight.replace(' kg', '') || '';
            document.getElementById('edit-weight-modal').showModal();
        }

        function openWeightModal(bookingId, currentWeight) {
            document.getElementById('edit-weight-booking-id').value = bookingId;
            document.getElementById('edit-booking-weight').value = currentWeight || '';
            document.getElementById('edit-weight-modal').showModal();
        }

        function openRescheduleModal(bookingId) {
            document.getElementById('reschedule-booking-id').value = bookingId;
            document.getElementById('reschedule-date').value = '';
            document.getElementById('reschedule-time').innerHTML = '<option value="">Select a date first</option>';
            document.getElementById('reschedule-modal').showModal();
        }

        // Load time slots when date changes
        document.getElementById('reschedule-date').addEventListener('change', async function() {
            const date = this.value;
            const timeSelect = document.getElementById('reschedule-time');
            
            if (!date) {
                timeSelect.innerHTML = '<option value="">Select a date first</option>';
                return;
            }

            timeSelect.innerHTML = '<option value="">Loading...</option>';

            try {
                const response = await fetch(`${window.bookingRoutes.slots}?date=${date}`);
                const result = await response.json();

                if (result.success && result.slots.length > 0) {
                    timeSelect.innerHTML = '<option value="">Select time slot</option>' + 
                        result.slots.map(slot => `<option value="${slot.time}">${slot.display}</option>`).join('');
                } else {
                    timeSelect.innerHTML = '<option value="">No available slots</option>';
                }
            } catch (error) {
                console.error('Error loading slots:', error);
                timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            }
        });

        // Handle reschedule form submission
        document.getElementById('reschedule-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const bookingId = document.getElementById('reschedule-booking-id').value;
            const date = document.getElementById('reschedule-date').value;
            const time = document.getElementById('reschedule-time').value;

            if (!date || !time) {
                showAlert('error', 'Please select both date and time');
                return;
            }

            try {
                const response = await fetch(window.bookingRoutes.reschedule.replace('__ID__', bookingId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.bookingRoutes.csrf
                    },
                    body: JSON.stringify({ booking_date: date, booking_time: time })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', result.message || 'Booking rescheduled successfully');
                    document.getElementById('reschedule-modal').close();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert('error', result.message || 'Failed to reschedule booking');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Failed to reschedule booking');
            }
        });

        async function cancelBooking(bookingId) {
            if (!confirm('Are you sure you want to cancel this booking?')) return;

            const reason = prompt('Cancellation reason (optional):');

            try {
                const response = await fetch(window.bookingRoutes.cancel.replace('__ID__', bookingId), {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.bookingRoutes.csrf
                    },
                    body: JSON.stringify({ reason: reason })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', result.message || 'Booking cancelled successfully');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert('error', result.message || 'Failed to cancel booking');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Failed to cancel booking');
            }
        }

        document.getElementById('edit-weight-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const bookingId = document.getElementById('edit-weight-booking-id').value;
            const weight = document.getElementById('edit-booking-weight').value;

            try {
                const response = await fetch(window.bookingRoutes.updateWeight.replace('__ID__', bookingId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.bookingRoutes.csrf
                    },
                    body: JSON.stringify({ weight: weight })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', result.message);
                    document.getElementById('edit-weight-modal').close();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert('error', result.message || 'Failed to update weight');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Failed to update weight');
            }
        });

        async function updateStatus(bookingId, newStatus) {
            try {
                const response = await fetch(window.bookingRoutes.updateStatus.replace('__ID__', bookingId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.bookingRoutes.csrf
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('success', result.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert('error', result.message || 'Failed to update status');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Failed to update status');
            }
        }

        async function viewDetails(bookingId) {
            try {
                const response = await fetch(window.bookingRoutes.details.replace('__ID__', bookingId));
                const result = await response.json();

                if (result.success) {
                    const booking = result.booking;
                    const detailsContent = document.getElementById('details-content');
                    
                    detailsContent.innerHTML = `
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Booking ID</p>
                                <p class="text-base font-semibold text-gray-900">#${booking.id}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Status</p>
                                <p class="text-base font-semibold text-gray-900">${booking.status}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-600">Customer</p>
                                <p class="text-base font-semibold text-gray-900">${booking.customer.name}</p>
                                <p class="text-sm text-gray-500">${booking.customer.email}</p>
                                <p class="text-sm text-gray-500">${booking.customer.phone}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Date</p>
                                <p class="text-base font-semibold text-gray-900">${booking.booking_date}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Time</p>
                                <p class="text-base font-semibold text-gray-900">${booking.booking_time}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-600">Pickup Address</p>
                                <p class="text-base text-gray-900">${booking.pickup_address}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Item Type</p>
                                <p class="text-base font-semibold text-gray-900">${booking.item_type}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Weight</p>
                                <p class="text-base font-semibold text-gray-900">${booking.weight}</p>
                            </div>
                            ${booking.services.length > 0 ? `
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-600 mb-2">Services</p>
                                <ul class="space-y-1">
                                    ${booking.services.map(s => `<li class="text-sm text-gray-900">• ${s.name} - ${s.price}</li>`).join('')}
                                </ul>
                            </div>
                            ` : ''}
                            ${booking.products.length > 0 ? `
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-600 mb-2">Products</p>
                                <ul class="space-y-1">
                                    ${booking.products.map(p => `<li class="text-sm text-gray-900">• ${p.name} - ${p.price}</li>`).join('')}
                                </ul>
                            </div>
                            ` : ''}
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-600">Notes</p>
                                <p class="text-base text-gray-900">${booking.notes}</p>
                            </div>
                            <div class="col-span-2 pt-4 border-t">
                                <p class="text-sm font-medium text-gray-600">Total Price</p>
                                <p class="text-2xl font-bold text-success">${booking.total_price}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500">Created: ${booking.created_at}</p>
                            </div>
                            <div class="col-span-2 pt-4 border-t space-y-2">
                                <div class="grid grid-cols-2 gap-2">
                                    <button onclick="editWeightFromView(${booking.id}, '${booking.weight}')" class="btn btn-warning">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                        </svg>
                                        Update Weight
                                    </button>
                                    ${booking.status !== 'Completed' && booking.status !== 'Cancelled' ? `
                                    <button onclick="document.getElementById('details-modal').close(); openRescheduleModal(${booking.id})" class="btn btn-primary">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Reschedule
                                    </button>
                                    ` : ''}
                                </div>
                                ${booking.status !== 'Completed' && booking.status !== 'Cancelled' ? `
                                <button onclick="document.getElementById('details-modal').close(); cancelBooking(${booking.id})" class="btn btn-error w-full">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancel Booking
                                </button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('details-modal').showModal();
                } else {
                    showAlert('error', 'Failed to load booking details');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Failed to load booking details');
            }
        }

        function showAlert(type, message) {
            const container = document.getElementById('alert-container');
            if (!container) return;

            const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
            const iconPath = type === 'success' 
                ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z';

            const alert = document.createElement('div');
            alert.className = `alert ${alertClass} mb-4`;
            alert.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}" />
                </svg>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;

            container.appendChild(alert);

            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }
    </script>
    @endpush
</x-layout>
