@php
    $isActive = fn ($name) => request()->routeIs($name);
@endphp

<!-- Admin Menu -->
<nav class="flex flex-col gap-2 p-4">
    <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200
                  {{ $isActive('admin.dashboard') ? 'bg-gradient-to-r from-green-500 to-teal-500 text-white font-semibold shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:shadow-sm' }}">
            <span class="text-3xl">🏠</span>
            <span class="text-base">Dashboard</span>
    </a>

    <!-- Profile -->
    <a href="{{ route('admin.profile') }}"
           class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200
                  {{ $isActive('admin.profile') ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:shadow-sm' }}">
            <span class="text-3xl">👨‍💼</span>
            <span class="text-base">Profile</span>
    </a>

    <!-- Change Password -->
    <a href="{{ route('admin.change-password') }}"
           class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200
                  {{ $isActive('admin.change-password') ? 'bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:shadow-sm' }}">
            <span class="text-3xl">🔐</span>
            <span class="text-base">Change Password</span>
    </a>

    <!-- Create Admin -->
    <a href="{{ route('admin.create-admin') }}"
           class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200
                  {{ $isActive('admin.create-admin') ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:shadow-sm' }}">
            <span class="text-3xl">👥</span>
            <span class="text-base">Create Admin</span>
    </a>

    <!-- Route to User -->
    <a href="{{ route('admin.route-to-user') }}"
           class="flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200
                  {{ $isActive('admin.route-to-user') ? 'bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-semibold shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:shadow-sm' }}">
            <span class="text-3xl">🗺️</span>
            <span class="text-base">Route to User</span>
    </a>
</nav>
