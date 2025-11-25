<x-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Welcome Card -->
        <div class="card bg-white shadow-md rounded-xl border border-gray-200 p-6">
            <h2 class="text-2xl font-bold text-blue-600 mb-2">Welcome, {{ Auth::user()->fname }} {{ Auth::user()->lname }}</h2>
            <p class="text-gray-700">This is your dashboard. You can book laundry, check status, view history, and more.</p>
        </div>

        <!-- Sample Placeholder Cards (for future sections) -->
        <div class="card bg-white shadow-sm rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Quick Booking</h3>
            <p class="text-sm text-gray-600">Start a new laundry booking in just a few clicks.</p>
        </div>

        <div class="card bg-white shadow-sm rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Laundry Status</h3>
            <p class="text-sm text-gray-600">Track your current laundry progress in real-time.</p>
        </div>
    </div>
</x-layout>
