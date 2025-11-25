@php
    $user = Auth::user();
@endphp

<nav class="bg-white border-b border-gray-200 shadow-sm fixed top-0 left-0 right-0 z-50">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between h-[5.5rem]">
        
        <!-- Branding -->
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/washhour_logo.png') }}" alt="Logo" class="w-10 h-10">
            <a href="{{ route('user.dashboard') }}" class="text-2xl font-extrabold text-blue-600">WashHour</a>
        </div>

        <!-- Right side: Search + Profile -->
        <div class="flex items-center gap-6">
            <!-- Search Bar -->
            <input type="text" placeholder="Search..." class="input input-bordered w-64">

            <!-- Interactive Profile Dropdown -->
            <div class="relative">
                <button onclick="document.getElementById('profileDropdown').classList.toggle('hidden')" class="flex items-center gap-2 focus:outline-none">
                    <!-- ✅ Name only, no avatar -->
                    <span class="text-sm text-gray-700 font-medium">{{ $user->fname }} {{ $user->lname }}</span>
                </button>

                <ul id="profileDropdown" class="absolute right-0 mt-2 w-52 bg-white shadow rounded-box text-gray-700 hidden z-50">
                    <li><a href="{{ route('user.profile') }}" class="block px-4 py-2 hover:bg-gray-100">👤 Profile</a></li>
                    <li><a href="{{ route('user.change-password') }}" class="block px-4 py-2 hover:bg-gray-100">🔒 Change Password</a></li>
                    <li>
                        <form action="{{ route('user.logout') }}" method="POST" class="block px-4 py-2 hover:bg-gray-100">
                            @csrf
                            <button type="submit" class="w-full text-left">🚪 Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
