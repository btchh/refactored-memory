@props(['isAdmin' => false])

@php
    $isLandingPage = request()->is('/') || request()->routeIs('landing');
    $anchor = fn($hash) => $isLandingPage ? $hash : url('/' . $hash);
@endphp

<nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/washhour_logo.png') }}" alt="Logo" class="h-10 w-10">
                <span class="text-xl font-bold text-primary-600">WashHour</span>
            </a>

            @if(!$isAdmin)
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ $anchor('#home') }}" class="text-gray-600 hover:text-primary-600 font-medium">Home</a>
                <a href="{{ $anchor('#about') }}" class="text-gray-600 hover:text-primary-600 font-medium">About</a>
                <a href="{{ $anchor('#services') }}" class="text-gray-600 hover:text-primary-600 font-medium">Services</a>
                <a href="{{ $anchor('#pricing') }}" class="text-gray-600 hover:text-primary-600 font-medium">Pricing</a>
            </div>

            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('user.login') }}" class="px-4 py-2 text-gray-700 hover:text-primary-600 font-medium">Login</a>
                <a href="{{ route('user.register') }}" class="px-5 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">Get Started</a>
            </div>

            <button id="guest-menu-toggle" class="md:hidden p-2 text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            @else
            <a href="{{ url('/') }}" class="px-4 py-2 text-gray-700 hover:text-primary-600 font-medium">← Back to Home</a>
            @endif
        </div>
    </div>

    @if(!$isAdmin)
    <div id="guest-mobile-menu" class="hidden md:hidden bg-white border-t">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ $anchor('#home') }}" class="block py-2 text-gray-600">Home</a>
            <a href="{{ $anchor('#about') }}" class="block py-2 text-gray-600">About</a>
            <a href="{{ $anchor('#services') }}" class="block py-2 text-gray-600">Services</a>
            <a href="{{ $anchor('#pricing') }}" class="block py-2 text-gray-600">Pricing</a>
            <div class="pt-3 border-t space-y-2">
                <a href="{{ route('user.login') }}" class="block w-full py-2 text-center border rounded-lg">Login</a>
                <a href="{{ route('user.register') }}" class="block w-full py-2 text-center bg-primary-600 text-white rounded-lg">Get Started</a>
            </div>
        </div>
    </div>
    @endif
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('guest-menu-toggle');
        const menu = document.getElementById('guest-mobile-menu');
        if (toggle && menu) {
            toggle.addEventListener('click', () => menu.classList.toggle('hidden'));
        }
    });
</script>
