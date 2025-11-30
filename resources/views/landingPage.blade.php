@extends('components.guest')

@section('title', 'Landing Page')

@section('content')
        <!-- Fixed navbar -->
        <nav class="navbar bg-white shadow-md h-20 fixed top-0 left-0 right-0 z-50">
        <div class="flex items-center justify-between h-full px-4 sm:px-6 lg:px-8">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
            <img src="{{ asset('images/washhour_logo.png') }}" alt="Logo" class="h-12 w-12">
            <span class="text-xl sm:text-2xl md:text-3xl font-bold text-blue-600 group-hover:text-blue-500 transition-colors">
                WashHour
            </span>
            </a>

            <!-- Nav links -->
            <div class="hidden md:flex space-x-6">
            <a href="#home" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Home</a>
            <a href="#about" class="nav-link text-gray-700 hover:text-blue-600 font-medium">About Us</a>
            <a href="#services" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Services</a>
            <a href="#products" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Products</a>
            <a href="#pricing" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Prices</a>
            </div>

            <!-- Auth links -->
            <div class="hidden md:flex space-x-4">
            <a href="{{ route('user.login') }}" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Login</a>
            <span class="text-gray-400">|</span>
            <a href="{{ route('user.register') }}" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Register</a>
            </div>

            <!-- Hamburger -->
            <div class="md:hidden">
            <button id="menu-toggle" class="text-gray-700 focus:outline-none" aria-label="Toggle navigation menu">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            </div>
        </div>
        </nav>

        <!-- Mobile dropdown -->
        <div id="mobile-menu" class="hidden md:hidden fixed top-20 left-0 right-0 z-40 bg-white shadow-lg border-t border-gray-200">
        <ul class="flex flex-col p-6 max-w-screen-lg mx-auto">
            <li class="border-b border-gray-100 last:border-0">
                <a href="#home" class="nav-link block py-3 px-4 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md transition-colors duration-150">Home</a>
            </li>
            <li class="border-b border-gray-100 last:border-0">
                <a href="#about" class="nav-link block py-3 px-4 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md transition-colors duration-150">About Us</a>
            </li>
            <li class="border-b border-gray-100 last:border-0">
                <a href="#services" class="nav-link block py-3 px-4 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md transition-colors duration-150">Services</a>
            </li>
            <li class="border-b border-gray-100 last:border-0">
                <a href="#products" class="nav-link block py-3 px-4 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md transition-colors duration-150">Products</a>
            </li>
            <li class="border-b border-gray-100 last:border-0">
                <a href="#pricing" class="nav-link block py-3 px-4 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md transition-colors duration-150">Prices</a>
            </li>
            <li class="pt-4 mt-4 border-t border-gray-200">
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('user.login') }}" class="btn btn-outline w-full">Login</a>
                    <a href="{{ route('user.register') }}" class="btn btn-primary w-full">Register</a>
                </div>
            </li>
        </ul>
        </div>

    <!-- Page wrapper -->
    <div class="flex flex-col min-h-screen overflow-x-hidden">
        <main class="grow">
            <!-- HOME SECTION -->
            <section id="home" class="relative w-screen h-screen flex items-center justify-start text-white">
                <img src="{{ asset('images/laundry.png') }}" alt="Laundry Background"
                     class="absolute inset-0 w-full h-full object-cover z-0" />
                <div class="absolute inset-0 bg-black/20 z-10"></div>

                <div class="relative z-20 w-full max-w-2xl px-8">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 drop-shadow-lg leading-tight">Wash Hour</h1>
                    <p class="text-xl md:text-2xl mb-4 font-medium">Where Clean Meets Smart, and Service Comes First.</p>
                    <p class="text-base md:text-lg mb-8 leading-relaxed">Experience hassle-free laundry booking, real-time updates, and eco-friendly service — all in one place.</p>
                    <a href="{{ route('user.register') }}"
                       class="btn btn-primary btn-lg">
                        Get Started
                    </a>
                </div>
            </section>


            <!-- ABOUT SECTION -->
            <section id="about" class="py-16 px-6 scroll-mt-[100px] bg-white">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-wide">About Us</h2>
                </div>

                <div class="max-w-6xl mx-auto">
                    <div class="flex flex-col md:flex-row items-center gap-12">
                        <!-- Left Side: Image -->
                        <div class="md:w-1/2 w-full">
                            <img src="{{ asset('images/laundry-img.jpg') }}" alt="Wash Hour Care"
                                class="rounded-xl shadow-md w-full max-w-md h-auto object-cover">
                        </div>

                        <!-- Right Side: Text Content -->
                        <div class="md:w-1/2 w-full space-y-8">
                            <div>
                                <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Your Smart Laundry Partner</h3>
                                <p class="text-gray-700 leading-relaxed">
                                    Wash Hour is built for modern living — combining professional care with smart scheduling, real-time updates, and location-aware delivery. We make laundry effortless, secure, and always on time.
                                </p>
                            </div>
                            <div>
                                <h4 class="text-xl font-semibold text-gray-900 mb-2">Personalized Support</h4>
                                <p class="text-gray-700 leading-relaxed">
                                    Whether you're booking online or walking into our shop — our team is always ready to assist. We value your time and ensure that every laundry concern is handled with care, attention, and a smile.
                                </p>
                            </div>
                            <div>
                                <h4 class="text-xl font-semibold text-gray-900 mb-2">Quality Care</h4>
                                <p class="text-gray-700 leading-relaxed">
                                    We handle your garments with precision — separating whites, colors, and delicates. Our detergents are gentle yet effective, preserving fabric quality.
                                </p>
                            </div>
                            <div>
                                <h4 class="text-xl font-semibold text-gray-900 mb-2">Seamless Booking</h4>
                                <p class="text-gray-700 leading-relaxed">
                                    Book your laundry slot through our platform. With Google Calendar integration, your schedule is synced and stress-free.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


           <!-- SERVICES SECTION -->
            <section id="services" class="bg-gray-50 py-16 text-center w-full px-6 scroll-mt-24">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-12">Our Services</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto">
                    
                    <!-- Wash Card -->
                    <div class="card hover:shadow-lg transition-shadow duration-200">
                        <div class="card-body flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Wash</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Gentle and thorough cleaning using premium detergents. We separate whites, colors, and delicates to protect every fabric.</p>
                        </div>
                    </div>

                    <!-- Dry Card -->
                    <div class="card hover:shadow-lg transition-shadow duration-200">
                        <div class="card-body flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Dry</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Fast and efficient tumble drying with temperature control to preserve softness and prevent shrinkage.</p>
                        </div>
                    </div>

                    <!-- Fold Card -->
                    <div class="card hover:shadow-lg transition-shadow duration-200">
                        <div class="card-body flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Fold</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Neatly folded garments, sorted and packed with care — ready for pickup or delivery.</p>
                        </div>
                    </div>

                    <!-- Delivery Card -->
                    <div class="card hover:shadow-lg transition-shadow duration-200">
                        <div class="card-body flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Delivery</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Convenient door-to-door service. Track your laundry in real-time and receive it fresh and on time.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- PRODUCTS SECTION -->
            <section id="products" class="bg-white py-16 text-center w-full px-6 scroll-mt-24">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-12">Our Products</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                    
                    <!-- Detergents Card -->
                    <div class="card hover:shadow-lg transition-shadow duration-200">
                        <div class="card-body flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Detergents</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Premium detergents that clean deeply while protecting fabric quality.</p>
                        </div>
                    </div>

                    <!-- Fabcon Card -->
                    <div class="card hover:shadow-lg transition-shadow duration-200">
                        <div class="card-body flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Fabcon</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Fabric conditioner that keeps clothes soft, fresh, and easy to iron.</p>
                        </div>
                    </div>

                    <!-- Downy Card -->
                    <div class="card hover:shadow-lg transition-shadow duration-200">
                        <div class="card-body flex flex-col items-center text-center">
                            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Downy</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Trusted Downy products for long-lasting fragrance and fabric care.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- PRICING SECTION -->
            <section id="pricing" class="relative py-16 text-center w-full px-6 scroll-mt-24 overflow-hidden bg-gray-50">
                <!-- Background image with reduced opacity -->
                <img src="{{ asset('images/price-bg.jpg') }}" alt="Price Background"
                    class="absolute inset-0 w-full h-full object-cover z-0 opacity-10" />

                <!-- Content -->
                <div class="relative z-10 max-w-6xl mx-auto">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-12">Our Pricing</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4 md:px-0">
                        <!-- Basic Services -->
                        <div class="card shadow-md">
                            <div class="card-body text-left">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">Basic Services</h3>
                                <ul class="space-y-3 text-sm">
                                    <li class="flex justify-between items-center">
                                        <span class="text-gray-700">Wash</span>
                                        <span class="font-semibold text-gray-900">₱70</span>
                                    </li>
                                    <li class="flex justify-between items-center">
                                        <span class="text-gray-700">Dry</span>
                                        <span class="font-semibold text-gray-900">₱70</span>
                                    </li>
                                    <li class="flex justify-between items-center">
                                        <span class="text-gray-700">Detergent</span>
                                        <span class="font-semibold text-gray-900">₱15</span>
                                    </li>
                                    <li class="flex justify-between items-center">
                                        <span class="text-gray-700">Fabric Conditioner</span>
                                        <span class="font-semibold text-gray-900">₱20</span>
                                    </li>
                                    <li class="flex justify-between items-center">
                                        <span class="text-gray-700">Fold</span>
                                        <span class="font-semibold text-gray-900">₱20</span>
                                    </li>
                                    <li class="flex justify-between items-center">
                                        <span class="text-gray-700">Delivery</span>
                                        <span class="font-semibold text-gray-900">₱20</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Comforter -->
                        <div class="card shadow-md">
                            <div class="card-body text-left">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">Comforter</h3>
                                <ul class="space-y-3 text-sm">
                                    <li class="flex justify-between items-center">
                                        <span class="text-gray-700">Single Piece</span>
                                        <span class="font-semibold text-gray-900">₱200</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Per Load -->
                        <div class="card shadow-md">
                            <div class="card-body text-left">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">Per Load (7 kg)</h3>
                                <ul class="space-y-3 text-sm">
                                    <li class="text-gray-700">
                                        <span class="font-semibold text-gray-900">Minimum Load:</span> 7 kg
                                    </li>
                                    <li class="flex justify-between items-center">
                                        <span class="text-gray-700">With Fold</span>
                                        <span class="font-semibold text-gray-900">₱195</span>
                                    </li>
                                    <li class="flex justify-between items-center">
                                        <span class="text-gray-700">Without Fold</span>
                                        <span class="font-semibold text-gray-900">₱175</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">

                    <!-- Left: Logo + Brand + Contact Info -->
                    <div class="flex flex-col items-center md:items-start text-left space-y-5">
                        <!-- Logo + Brand -->
                        <a href="{{ url('/') }}" class="flex items-center gap-3 group" aria-label="Go to Home">
                            <img src="{{ asset('images/washhour_logo.png') }}" alt="WashHour Logo" class="h-10 w-10">
                            <span class="text-xl sm:text-2xl font-bold text-blue-400 group-hover:text-blue-300 transition-colors duration-150">
                                WashHour
                            </span>
                        </a>

                        <!-- Location -->
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-sm md:text-base text-gray-300 leading-relaxed">
                                B6 L15 CITY PARK AVE., CITY PARK SUBDIVISION, SABANG LIPA
                            </span>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-sm md:text-base text-gray-300">0921-776-9999</span>
                        </div>
                    </div>

                    <!-- Right: Copyright (centered vertically) -->
                    <div class="flex items-center justify-center md:justify-end text-gray-400 text-sm">
                        &copy; {{ date('Y') }} WashHour. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>

  
    <!-- Scripts -->
    @vite(['resources/js/pages/landing.js'])
@endsection

