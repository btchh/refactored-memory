@php
    $isActive = fn ($name) => request()->routeIs($name);
@endphp

<aside class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-5 h-full">
    <!-- Menu only, no profile header -->
    <nav class="flex flex-col gap-2">
        <!-- Dashboard -->
        <a href="{{ route('user.dashboard') }}"
           class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200
                  {{ $isActive('user.dashboard') ? 'bg-gradient-to-r from-green-500 to-teal-500 text-white font-semibold shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:shadow-sm' }}">
            <span class="text-3xl">🏠</span>
            <span class="text-base">Dashboard</span>
        </a>

        <!-- Book Laundry -->
        <a href="{{ route('user.booking') }}"
           class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200
                  {{ $isActive('user.booking') ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white font-semibold shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:shadow-sm' }}">
            <span class="text-3xl">🧺</span>
            <span class="text-base">Book Laundry</span>
        </a>

        <!-- Laundry Status -->
        <a href="{{ route('user.status') }}"
           class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200
                  {{ $isActive('user.status') ? 'bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:shadow-sm' }}">
            <span class="text-3xl">📊</span>
            <span class="text-base">Laundry Status</span>
        </a>

        <!-- Shop Location -->
        <a href="{{ route('user.shop-location') }}"
           class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200
                  {{ $isActive('user.shop-location') ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white font-semibold shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:shadow-sm' }}">
            <span class="text-3xl">📍</span>
            <span class="text-base">Location</span>
        </a>

        <!-- History Booking -->
        <a href="{{ route('user.history') }}"
           class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200
                  {{ $isActive('user.history') ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:shadow-sm' }}">
            <span class="text-3xl">📜</span>
            <span class="text-base">History Booking</span>
        </a>
    </nav>
</aside>

