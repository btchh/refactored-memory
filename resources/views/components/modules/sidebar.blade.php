@php
    $isActive = fn ($name) => request()->routeIs($name);
@endphp

<aside class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-5 h-full">
    <!-- Menu only, no profile header -->
    <nav class="flex flex-col gap-1">
        <!-- Dashboard -->
        <a href="{{ route('user.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg
                  {{ $isActive('user.dashboard') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
            <img src="{{ asset('images/home.png') }}" alt="Dashboard" class="w-5 h-5">
            <span>Dashboard</span>
        </a>

        <!-- Book Laundry -->
        <a href="{{ route('user.booking') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg
                  {{ $isActive('user.booking') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
            <img src="{{ asset('images/clipboard.png') }}" alt="Book Laundry" class="w-5 h-5">
            <span>Book Laundry</span>
        </a>

        <!-- Laundry Status -->
        <a href="{{ route('user.status') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg
                  {{ $isActive('user.status') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
            <img src="{{ asset('images/refresh.png') }}" alt="Laundry Status" class="w-5 h-5">
            <span>Laundry Status</span>
        </a>

        <!-- Shop Location -->
        <a href="{{ route('user.shop-location') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg
                  {{ $isActive('user.shop-location') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
            <img src="{{ asset('images/map.png') }}" alt="Shop Location" class="w-5 h-5">
            <span>Location</span>
        </a>

        <!-- History Booking -->
        <a href="{{ route('user.history') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg
                  {{ $isActive('user.history') ? 'bg-green-100 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
            <img src="{{ asset('images/history.png') }}" alt="History Booking" class="w-5 h-5">
            <span>History Booking</span>
        </a>
    </nav>
</aside>

