@php
    $isAdmin = Auth::guard('admin')->check();
    $user = $isAdmin ? Auth::guard('admin')->user() : Auth::user();
    $userName = $isAdmin 
        ? ($user->fname ?? '') . ' ' . ($user->lname ?? '') 
        : ($user->fname ?? '') . ' ' . ($user->lname ?? '');
@endphp

<nav class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg fixed top-0 left-0 right-0" style="z-index: 9998 !important;">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between h-[5.5rem]">
        
        <!-- Branding -->
        <div class="flex items-center gap-3">
            <div class="bg-white rounded-full p-2 shadow-lg">
                <img src="{{ asset('images/washhour_logo.png') }}" alt="Logo" class="w-8 h-8">
            </div>
            <a href="{{ $isAdmin ? route('admin.dashboard') : route('user.dashboard') }}" class="text-2xl font-extrabold text-white hover:text-blue-100 transition-colors">
                WashHour
            </a>
        </div>

        <!-- Right side: Profile -->
        <div class="flex items-center gap-4">
            <!-- User Type Badge -->
            @if($isAdmin)
                <span class="px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-xs font-bold">ADMIN</span>
            @endif

            <!-- Interactive Profile Dropdown -->
            <div class="relative z-[9999]" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="flex items-center gap-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-white/50">
                    <div class="bg-white rounded-full w-8 h-8 flex items-center justify-center">
                        <span class="text-lg">{{ $isAdmin ? '👨‍💼' : '👤' }}</span>
                    </div>
                    <span class="text-sm text-white font-medium">{{ trim($userName) }}</span>
                    <svg class="w-4 h-4 text-white transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <ul x-show="open" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden"
                    style="display: none; z-index: 99999 !important;">
                    
                    <!-- Profile Header -->
                    <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-200">
                        <p class="text-xs text-gray-500 mb-1 font-medium">{{ $isAdmin ? 'Admin Account' : 'User Account' }}</p>
                        <p class="text-base font-bold text-gray-800 truncate">{{ trim($userName) }}</p>
                        <p class="text-sm text-gray-600 truncate">{{ $user->email ?? '' }}</p>
                    </div>

                    @if($isAdmin)
                        <li><a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 transition-colors">
                            <span class="text-2xl">👤</span>
                            <span class="text-base text-gray-700 font-medium">Profile</span>
                        </a></li>
                        <li><a href="{{ route('admin.change-password') }}" class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 transition-colors">
                            <span class="text-2xl">🔒</span>
                            <span class="text-base text-gray-700 font-medium">Change Password</span>
                        </a></li>
                        <li class="border-t border-gray-200">
                            <form action="{{ route('admin.logout') }}" method="POST" class="block">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 px-5 py-4 hover:bg-red-50 transition-colors w-full text-left">
                                    <span class="text-2xl">🚪</span>
                                    <span class="text-base text-red-600 font-semibold">Logout</span>
                                </button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('user.profile') }}" class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 transition-colors">
                            <span class="text-2xl">👤</span>
                            <span class="text-base text-gray-700 font-medium">Profile</span>
                        </a></li>
                        <li><a href="{{ route('user.change-password') }}" class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50 transition-colors">
                            <span class="text-2xl">🔒</span>
                            <span class="text-base text-gray-700 font-medium">Change Password</span>
                        </a></li>
                        <li class="border-t border-gray-200">
                            <form action="{{ route('user.logout') }}" method="POST" class="block">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 px-5 py-4 hover:bg-red-50 transition-colors w-full text-left">
                                    <span class="text-2xl">🚪</span>
                                    <span class="text-base text-red-600 font-semibold">Logout</span>
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
