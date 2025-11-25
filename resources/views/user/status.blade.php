<x-layout>
    <x-slot name="title">Laundry Status</x-slot>

    <div class="container mx-auto px-4 py-6">
        <!-- Page Title -->
        <h1 class="text-2xl font-bold text-blue-600 mb-6">Laundry Status</h1>

        <!-- Status Table Card -->
        <div class="card bg-white rounded-xl shadow-md border border-gray-200">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2">#</th>
                                <th class="px-4 py-2">Services</th>
                                <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">Time</th>
                                <th class="px-4 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $index => $booking)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2">
                                        @foreach($booking->services as $service)
                                            <span class="badge badge-outline mr-1">{{ $service['name'] }}</span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-2">{{ $booking->date }}</td>
                                    <td class="px-4 py-2">{{ $booking->time }}</td>
                                    <td class="px-4 py-2">
                                        @if($booking->status === 'Pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($booking->status === 'In Progress')
                                            <span class="badge badge-info">In Progress</span>
                                        @elseif($booking->status === 'Completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($booking->status === 'Delivered')
                                            <span class="badge badge-primary">Delivered</span>
                                        @else
                                            <span class="badge">Unknown</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-6">
                                        No bookings yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout>
