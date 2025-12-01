<x-guest>
    <x-slot:title>Welcome</x-slot:title>

    <!-- Fixed Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/washhour_logo.png') }}" alt="Logo" class="h-10 w-10">
                    <span class="text-xl font-bold text-primary-600">WashHour</span>
                </a>

                <div class="hidden md:flex items-center gap-8">
                    <a href="#home" class="text-gray-600 hover:text-primary-600 font-medium">Home</a>
                    <a href="#about" class="text-gray-600 hover:text-primary-600 font-medium">About</a>
                    <a href="#services" class="text-gray-600 hover:text-primary-600 font-medium">Services</a>
                    <a href="#pricing" class="text-gray-600 hover:text-primary-600 font-medium">Pricing</a>
                </div>

                <div class="hidden md:flex items-center gap-3">
                    <a href="{{ route('user.login') }}" class="px-4 py-2 text-gray-700 hover:text-primary-600 font-medium">Login</a>
                    <a href="{{ route('user.register') }}" class="px-5 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium">Get Started</a>
                </div>

                <button id="menu-toggle" class="md:hidden p-2 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-4 py-4 space-y-3">
                <a href="#home" class="block py-2 text-gray-600">Home</a>
                <a href="#about" class="block py-2 text-gray-600">About</a>
                <a href="#services" class="block py-2 text-gray-600">Services</a>
                <a href="#pricing" class="block py-2 text-gray-600">Pricing</a>
                <div class="pt-3 border-t space-y-2">
                    <a href="{{ route('user.login') }}" class="block w-full py-2 text-center border rounded-lg">Login</a>
                    <a href="{{ route('user.register') }}" class="block w-full py-2 text-center bg-primary-600 text-white rounded-lg">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center pt-16">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/laundry.png') }}" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900/80 to-gray-900/40"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="max-w-2xl">
                <span class="inline-block px-4 py-1 bg-primary-500/20 text-primary-300 rounded-full text-sm font-medium mb-6">
                    #1 Laundry Service in Lipa
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                    Fresh Clothes,<br><span class="text-primary-400">Zero Hassle</span>
                </h1>
                <p class="text-lg text-gray-300 mb-8">
                    Book your laundry pickup in seconds. Track in real-time. Get fresh, clean clothes delivered to your door.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('user.register') }}" class="px-8 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-semibold text-center">
                        Book Now — It's Free
                    </a>
                    <a href="#services" class="px-8 py-3 bg-white/10 text-white rounded-lg hover:bg-white/20 font-semibold text-center backdrop-blur-sm">
                        See How It Works
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-white py-12 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <p class="text-3xl font-bold text-primary-600">500+</p>
                    <p class="text-gray-600 text-sm mt-1">Happy Customers</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-primary-600">24hr</p>
                    <p class="text-gray-600 text-sm mt-1">Turnaround Time</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-primary-600">100%</p>
                    <p class="text-gray-600 text-sm mt-1">Satisfaction Rate</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-primary-600">Free</p>
                    <p class="text-gray-600 text-sm mt-1">Pickup & Delivery</p>
                </div>
            </div>
        </div>
    </section>


    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <img src="{{ asset('images/laundry-img.jpg') }}" alt="About WashHour" class="rounded-2xl shadow-xl w-full">
                </div>
                <div>
                    <span class="text-primary-600 font-semibold text-sm uppercase tracking-wide">About Us</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2 mb-6">Your Smart Laundry Partner</h2>
                    <p class="text-gray-600 mb-6">
                        WashHour combines professional laundry care with modern technology. Book online, track your order in real-time, and enjoy fresh clothes delivered right to your doorstep.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Premium Quality Care</h4>
                                <p class="text-gray-600 text-sm">We separate colors, whites, and delicates for optimal cleaning.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Real-Time Tracking</h4>
                                <p class="text-gray-600 text-sm">Know exactly where your laundry is at every step.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Eco-Friendly Products</h4>
                                <p class="text-gray-600 text-sm">Gentle on fabrics, tough on stains, kind to the environment.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wide">Our Services</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2">How It Works</h2>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">Simple, fast, and reliable laundry service in just 3 easy steps</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-16">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="text-4xl font-bold text-primary-100 mb-2">01</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Book Online</h3>
                    <p class="text-gray-600">Schedule a pickup time that works for you.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <div class="text-4xl font-bold text-primary-100 mb-2">02</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">We Pick Up</h3>
                    <p class="text-gray-600">Our team collects your laundry from your doorstep.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-4xl font-bold text-primary-100 mb-2">03</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Fresh Delivery</h3>
                    <p class="text-gray-600">Get your clean clothes delivered within 24 hours.</p>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Wash & Dry</h3>
                    <p class="text-gray-600 text-sm">Professional washing with premium detergents.</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Fold & Pack</h3>
                    <p class="text-gray-600 text-sm">Neatly folded and ready for your closet.</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Comforters</h3>
                    <p class="text-gray-600 text-sm">Special care for blankets and bedding.</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Express Service</h3>
                    <p class="text-gray-600 text-sm">Same-day service available.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-gray-50 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wide">Pricing</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mt-2">Simple, Transparent Pricing</h2>
                <p class="text-gray-600 mt-4">No hidden fees. Pay only for what you need.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-shadow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Basic Services</h3>
                    <p class="text-gray-500 text-sm mb-6">Per item pricing</p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex justify-between"><span class="text-gray-600">Wash</span><span class="font-semibold">₱70</span></li>
                        <li class="flex justify-between"><span class="text-gray-600">Dry</span><span class="font-semibold">₱70</span></li>
                        <li class="flex justify-between"><span class="text-gray-600">Fold</span><span class="font-semibold">₱20</span></li>
                        <li class="flex justify-between"><span class="text-gray-600">Detergent</span><span class="font-semibold">₱15</span></li>
                        <li class="flex justify-between"><span class="text-gray-600">Fabric Conditioner</span><span class="font-semibold">₱20</span></li>
                    </ul>
                </div>

                <div class="bg-primary-600 rounded-2xl p-8 shadow-xl relative transform md:-translate-y-4">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full">MOST POPULAR</div>
                    <h3 class="text-xl font-bold text-white mb-2">Per Load (7kg)</h3>
                    <p class="text-primary-200 text-sm mb-6">Best value for families</p>
                    <ul class="space-y-4 mb-8 text-white">
                        <li class="flex justify-between"><span class="text-primary-100">With Fold</span><span class="font-semibold">₱195</span></li>
                        <li class="flex justify-between"><span class="text-primary-100">Without Fold</span><span class="font-semibold">₱175</span></li>
                        <li class="flex justify-between"><span class="text-primary-100">Delivery</span><span class="font-semibold">₱20</span></li>
                    </ul>
                    <a href="{{ route('user.register') }}" class="block w-full py-3 bg-white text-primary-600 rounded-lg font-semibold text-center hover:bg-gray-100 transition-colors">
                        Get Started
                    </a>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-shadow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Comforter</h3>
                    <p class="text-gray-500 text-sm mb-6">Blankets & bedding</p>
                    <div class="text-center py-8">
                        <span class="text-5xl font-bold text-gray-900">₱200</span>
                        <p class="text-gray-500 mt-2">per piece</p>
                    </div>
                    <p class="text-gray-600 text-sm text-center">Includes wash, dry, and fold for all sizes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">Ready to Save Time on Laundry?</h2>
            <p class="text-primary-100 text-lg mb-8">Join hundreds of happy customers who trust WashHour.</p>
            <a href="{{ route('user.register') }}" class="inline-block px-8 py-4 bg-white text-primary-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                Start Your First Order — Free Pickup
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <a href="{{ url('/') }}" class="flex items-center gap-2 mb-4">
                        <img src="{{ asset('images/washhour_logo.png') }}" alt="Logo" class="h-10 w-10">
                        <span class="text-xl font-bold text-white">WashHour</span>
                    </a>
                    <p class="text-gray-400 text-sm">Your trusted laundry partner in Lipa City.</p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Contact Us</h4>
                    <div class="space-y-3 text-gray-400 text-sm">
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            B6 L15 City Park Ave., Sabang, Lipa
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            0921-776-9999
                        </p>
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <div class="space-y-2 text-gray-400 text-sm">
                        <a href="{{ route('user.login') }}" class="block hover:text-white">Login</a>
                        <a href="{{ route('user.register') }}" class="block hover:text-white">Register</a>
                        <a href="#services" class="block hover:text-white">Services</a>
                        <a href="#pricing" class="block hover:text-white">Pricing</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500 text-sm">
                © {{ date('Y') }} WashHour. All rights reserved.
            </div>
        </div>
    </footer>

    @push('scripts')
        @vite(['resources/js/pages/landing.js'])
    @endpush
</x-guest>
