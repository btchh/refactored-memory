@props(['isAdmin' => false])

@php
    $isLandingPage = request()->is('/') || request()->routeIs('landing');
    $anchor = fn($hash) => $isLandingPage ? $hash : url('/' . $hash);
@endphp

<nav class="fixed top-0 left-0 right-0 z-50 bg-primary-600 backdrop-blur-sm border-b border-primary-700 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="bg-white rounded-full p-1 flex items-center justify-center">
                    <img src="{{ asset('images/washhour_logo.png') }}" alt="Logo" class="h-10 w-10">
                </div>
                <span class="text-xl font-bold text-white">WashHour</span>
            </a>

            @if(!$isAdmin)
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ $anchor('#home') }}" class="text-white hover:text-primary-100 font-medium transition-colors">Home</a>
                <a href="{{ $anchor('#about') }}" class="text-white hover:text-primary-100 font-medium transition-colors">About</a>
                <a href="{{ $anchor('#services') }}" class="text-white hover:text-primary-100 font-medium transition-colors">Services</a>
                <a href="{{ $anchor('#pricing') }}" class="text-white hover:text-primary-100 font-medium transition-colors">Pricing</a>
            </div>

            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('user.login') }}" class="px-4 py-2 text-white hover:text-primary-100 font-medium transition-colors">Login</a>
                <a href="{{ route('user.register') }}" class="px-5 py-2 bg-white text-primary-600 rounded-lg hover:bg-primary-50 font-medium transition-colors">Get Started</a>
            </div>

            <button id="guest-menu-toggle" class="md:hidden p-2 text-white hover:text-primary-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            @else
            <a href="{{ url('/') }}" class="px-4 py-2 text-white hover:text-primary-100 font-medium transition-colors">← Back to Home</a>
            @endif
        </div>
    </div>

    @if(!$isAdmin)
    <div id="guest-mobile-menu" class="hidden md:hidden bg-primary-700 border-t border-primary-800">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ $anchor('#home') }}" class="block py-2 text-white hover:text-primary-100">Home</a>
            <a href="{{ $anchor('#about') }}" class="block py-2 text-white hover:text-primary-100">About</a>
            <a href="{{ $anchor('#services') }}" class="block py-2 text-white hover:text-primary-100">Services</a>
            <a href="{{ $anchor('#pricing') }}" class="block py-2 text-white hover:text-primary-100">Pricing</a>
            <div class="pt-3 border-t border-primary-600 space-y-2">
                <a href="{{ route('user.login') }}" class="block w-full py-2 text-center border border-white text-white rounded-lg hover:bg-primary-600">Login</a>
                <a href="{{ route('user.register') }}" class="block w-full py-2 text-center bg-white text-primary-600 rounded-lg hover:bg-primary-50">Get Started</a>
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
