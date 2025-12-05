@props(['isAdmin' => false])

@php
    $isLandingPage = request()->is('/') || request()->routeIs('landing');
    $anchor = fn($hash) => $isLandingPage ? $hash : url('/' . $hash);
@endphp

<nav class="navbar fixed top-0 left-0 right-0">
    <div class="max-w-7xl mx-auto px-6 h-full flex items-center justify-between">
        <a href="{{ url('/') }}" class="navbar-brand">
            <div class="bg-white rounded-full p-2">
                <img src="{{ asset('images/washhour_logo.png') }}" alt="Logo" class="h-8 w-8">
            </div>
            <span class="navbar-title">WashHour</span>
        </a>

        @if(!$isAdmin)
        <div class="hidden md:flex items-center gap-8">
            <a href="{{ $anchor('#home') }}" class="text-white/90 hover:text-white font-semibold transition-colors">Home</a>
            <a href="{{ $anchor('#about') }}" class="text-white/90 hover:text-white font-semibold transition-colors">About</a>
            <a href="{{ $anchor('#services') }}" class="text-white/90 hover:text-white font-semibold transition-colors">Services</a>
            <a href="{{ $anchor('#pricing') }}" class="text-white/90 hover:text-white font-semibold transition-colors">Pricing</a>
        </div>

        <div class="hidden md:flex items-center gap-3">
            <a href="{{ route('user.login') }}" class="px-5 py-2.5 text-white font-semibold hover:bg-white/10 rounded-md transition-colors">Login</a>
            <a href="{{ route('user.register') }}" class="btn btn-sm bg-white text-wash hover:bg-gray-50">Get Started</a>
        </div>

        <button id="guest-menu-toggle" class="md:hidden p-2 text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        @else
        <a href="{{ url('/') }}" class="text-white font-semibold hover:bg-white/10 px-4 py-2 rounded-md transition-colors">← Back</a>
        @endif
    </div>

    @if(!$isAdmin)
    <div id="guest-mobile-menu" class="hidden md:hidden bg-wash-dark border-t border-white/10">
        <div class="px-6 py-6 space-y-3">
            <a href="{{ $anchor('#home') }}" class="block py-3 text-white font-semibold">Home</a>
            <a href="{{ $anchor('#about') }}" class="block py-3 text-white font-semibold">About</a>
            <a href="{{ $anchor('#services') }}" class="block py-3 text-white font-semibold">Services</a>
            <a href="{{ $anchor('#pricing') }}" class="block py-3 text-white font-semibold">Pricing</a>
            <div class="pt-4 border-t border-white/10 space-y-3">
                <a href="{{ route('user.login') }}" class="block w-full py-3 text-center border-2 border-white text-white rounded-md font-semibold">Login</a>
                <a href="{{ route('user.register') }}" class="block w-full py-3 text-center bg-white text-wash rounded-md font-semibold">Get Started</a>
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
