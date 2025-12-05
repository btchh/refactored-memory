@php
    $isAdmin = Auth::guard('admin')->check();
    $user = $isAdmin ? Auth::guard('admin')->user() : Auth::user();
    $userName = $isAdmin 
        ? ($user->fname ?? '') . ' ' . ($user->lname ?? '') 
        : ($user->fname ?? '') . ' ' . ($user->lname ?? '');
    $userInitials = strtoupper(substr($user->fname ?? 'U', 0, 1) . substr($user->lname ?? 'S', 0, 1));
@endphp

<nav class="navbar fixed top-0 left-0 right-0" style="z-index: 9998;">
    <div class="container mx-auto px-6 h-full flex items-center justify-between">
        
        <!-- Branding -->
        <a href="{{ $isAdmin ? route('admin.dashboard') : route('user.dashboard') }}" class="navbar-brand">
            <div class="bg-white rounded-full p-2">
                <img src="{{ asset('images/washhour_logo.png') }}" alt="WashHour Logo" class="h-8 w-8">
            </div>
            <span class="navbar-title hover:text-white/90 transition-colors">WashHour</span>
        </a>

        <!-- Right side: Profile -->
        <div class="flex items-center gap-4">
            <!-- User Type Badge -->
            @if($isAdmin)
                <span class="badge badge-warning">ADMIN</span>
            @endif

            <!-- Interactive Profile Dropdown -->
            <div class="relative z-[9999]" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="flex items-center gap-3 px-4 py-2.5 rounded-md border-2 border-white/20 bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-all focus:outline-none focus:ring-4 focus:ring-white/30 touch-target">
                    <!-- User Avatar with Initials -->
                    <div class="w-9 h-9 rounded-full bg-white text-wash flex items-center justify-center font-bold text-sm">
                        {{ $userInitials }}
                    </div>
                    <span class="text-sm text-white font-semibold hidden sm:inline">{{ trim($userName) }}</span>
                    <!-- Chevron Down Icon -->
                    <svg class="w-4 h-4 text-white transition-transform hidden sm:block" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <ul x-show="open" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="absolute right-0 mt-2 w-56 sm:w-64 bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden"
                    style="display: none; z-index: 99999;">
                    
                    <!-- Profile Header -->
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <p class="text-xs text-gray-500 mb-1 font-medium">{{ $isAdmin ? 'Admin Account' : 'User Account' }}</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ trim($userName) }}</p>
                        <p class="text-xs text-gray-600 truncate">{{ $user->email ?? '' }}</p>
                    </div>

                    @if($isAdmin)
                        <li>
                            <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <!-- User Icon -->
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-sm text-gray-700 font-medium">Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.change-password') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <!-- Lock Icon -->
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <span class="text-sm text-gray-700 font-medium">Change Password</span>
                            </a>
                        </li>
                        <li class="border-t border-gray-200">
                            <form action="{{ route('admin.logout') }}" method="POST" class="block" data-no-protection>
                                @csrf
                                <button type="submit" class="flex items-center gap-3 px-4 py-3 hover:bg-red-50 transition-colors w-full text-left touch-target">
                                    <!-- Logout Icon -->
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span class="text-sm text-red-600 font-semibold">Logout</span>
                                </button>
                            </form>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('user.profile') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <!-- User Icon -->
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-sm text-gray-700 font-medium">Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.change-password') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                <!-- Lock Icon -->
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <span class="text-sm text-gray-700 font-medium">Change Password</span>
                            </a>
                        </li>
                        <li class="border-t border-gray-200">
                            <form action="{{ route('user.logout') }}" method="POST" class="block" data-no-protection>
                                @csrf
                                <button type="submit" class="flex items-center gap-3 px-4 py-3 hover:bg-red-50 transition-colors w-full text-left touch-target">
                                    <!-- Logout Icon -->
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span class="text-sm text-red-600 font-semibold">Logout</span>
                                </button>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Alpine.js for dropdown functionality -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
