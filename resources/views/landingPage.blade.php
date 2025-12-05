<x-guest :showNav="true">
    <x-slot:title>Welcome</x-slot:title>

    <!-- Hero -->
    <section id="home" class="relative min-h-screen flex items-center pt-20 overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/laundry.png') }}" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-br from-wash/95 via-wash-dark/90 to-gray-900/95"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-6 py-24">
            <div class="max-w-3xl">
                <div class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-bold mb-8">
                    🏆 Lipa's #1 Laundry Service
                </div>
                <h1 class="text-6xl md:text-7xl font-black text-white mb-6 leading-tight">
                    Fresh Laundry,<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-white/60">Delivered Fast</span>
                </h1>
                <p class="text-xl text-white/90 mb-10 leading-relaxed">
                    Professional laundry service with real-time tracking. Book in 60 seconds, get fresh clothes in 24 hours.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('user.register') }}" class="btn btn-lg bg-white text-wash hover:bg-gray-50 shadow-xl">
                        Start Free Booking
                    </a>
                    <a href="#services" class="btn btn-lg btn-ghost text-white border-2 border-white/30 hover:bg-white/10">
                        How It Works
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="bg-white py-16 border-b-2 border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <p class="text-4xl font-black text-wash mb-2">500+</p>
                    <p class="text-gray-600 font-medium">Happy Customers</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-wash mb-2">24hr</p>
                    <p class="text-gray-600 font-medium">Turnaround</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-wash mb-2">100%</p>
                    <p class="text-gray-600 font-medium">Satisfaction</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-wash mb-2">Free</p>
                    <p class="text-gray-600 font-medium">Pickup & Delivery</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about" class="py-24 bg-gray-50 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <img src="{{ asset('images/laundry-img.jpg') }}" alt="About" class="rounded-2xl shadow-2xl">
                </div>
                <div>
                    <div class="inline-block px-3 py-1 bg-wash/10 text-wash rounded-full text-sm font-bold mb-4">
                        ABOUT US
                    </div>
                    <h2 class="text-5xl font-black text-gray-900 mb-6 leading-tight">
                        Your Smart Laundry Partner
                    </h2>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        WashHour combines professional care with modern technology. Book online, track in real-time, and enjoy fresh clothes delivered to your door.
                    </p>
                    <div class="space-y-5">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Premium Quality Care</h4>
                                <p class="text-gray-600">Separate colors, whites, and delicates for optimal cleaning.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Real-Time Tracking</h4>
                                <p class="text-gray-600">Know exactly where your laundry is at every step.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-1">Eco-Friendly Products</h4>
                                <p class="text-gray-600">Gentle on fabrics, tough on stains, kind to the environment.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section id="services" class="py-24 bg-white scroll-mt-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div class="inline-block px-3 py-1 bg-wash/10 text-wash rounded-full text-sm font-bold mb-4">
                    OUR SERVICES
                </div>
                <h2 class="text-5xl font-black text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Three simple steps to fresh, clean laundry</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-20">
                <div class="relative">
                    <div class="absolute -top-4 -left-4 w-16 h-16 bg-wash/10 rounded-2xl flex items-center justify-center">
                        <span class="text-3xl font-black text-wash">1</span>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-8 pt-12">
                        <div class="w-16 h-16 bg-wash rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Book Online</h3>
                        <p class="text-gray-600 leading-relaxed">Schedule a pickup time that works for you in just 60 seconds.</p>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -top-4 -left-4 w-16 h-16 bg-wash/10 rounded-2xl flex items-center justify-center">
                        <span class="text-3xl font-black text-wash">2</span>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-8 pt-12">
                        <div class="w-16 h-16 bg-wash rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">We Pick Up</h3>
                        <p class="text-gray-600 leading-relaxed">Our team collects your laundry from your doorstep.</p>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -top-4 -left-4 w-16 h-16 bg-wash/10 rounded-2xl flex items-center justify-center">
                        <span class="text-3xl font-black text-wash">3</span>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-8 pt-12">
                        <div class="w-16 h-16 bg-wash rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Fresh Delivery</h3>
                        <p class="text-gray-600 leading-relaxed">Get your clean clothes delivered within 24 hours.</p>
                    </div>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 rounded-xl p-6 border-2 border-transparent hover:border-wash transition-all">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Wash & Dry</h3>
                    <p class="text-gray-600 text-sm">Professional washing with premium detergents.</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 border-2 border-transparent hover:border-wash transition-all">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Fold & Pack</h3>
                    <p class="text-gray-600 text-sm">Neatly folded and ready for your closet.</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 border-2 border-transparent hover:border-wash transition-all">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Comforters</h3>
                    <p class="text-gray-600 text-sm">Special care for blankets and bedding.</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 border-2 border-transparent hover:border-wash transition-all">
                    <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Express Service</h3>
                    <p class="text-gray-600 text-sm">Same-day service available.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-24 bg-gray-50 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div class="inline-block px-3 py-1 bg-wash/10 text-wash rounded-full text-sm font-bold mb-4">
                    PRICING
                </div>
                <h2 class="text-5xl font-black text-gray-900 mb-4">Simple, Transparent Pricing</h2>
                <p class="text-xl text-gray-600">No hidden fees. Pay only for what you need.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="bg-white rounded-2xl p-8 border-2 border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Basic Services</h3>
                    <p class="text-gray-600 mb-8">Per item pricing</p>
                    <ul class="space-y-4">
                        <li class="flex justify-between items-center">
                            <span class="text-gray-700">Wash</span>
                            <span class="text-xl font-bold text-gray-900">₱70</span>
                        </li>
                        <li class="flex justify-between items-center">
                            <span class="text-gray-700">Dry</span>
                            <span class="text-xl font-bold text-gray-900">₱70</span>
                        </li>
                        <li class="flex justify-between items-center">
                            <span class="text-gray-700">Fold</span>
                            <span class="text-xl font-bold text-gray-900">₱20</span>
                        </li>
                        <li class="flex justify-between items-center">
                            <span class="text-gray-700">Detergent</span>
                            <span class="text-xl font-bold text-gray-900">₱15</span>
                        </li>
                        <li class="flex justify-between items-center">
                            <span class="text-gray-700">Fabric Conditioner</span>
                            <span class="text-xl font-bold text-gray-900">₱20</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-wash rounded-2xl p-8 text-white relative transform md:-translate-y-4 shadow-2xl">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-2 bg-warning text-gray-900 text-xs font-black rounded-full uppercase">
                        Most Popular
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Per Load (7kg)</h3>
                    <p class="text-white/80 mb-8">Best value for families</p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex justify-between items-center">
                            <span class="text-white/90">With Fold</span>
                            <span class="text-xl font-bold">₱195</span>
                        </li>
                        <li class="flex justify-between items-center">
                            <span class="text-white/90">Without Fold</span>
                            <span class="text-xl font-bold">₱175</span>
                        </li>
                        <li class="flex justify-between items-center">
                            <span class="text-white/90">Delivery</span>
                            <span class="text-xl font-bold">₱20</span>
                        </li>
                    </ul>
                    <a href="{{ route('user.register') }}" class="btn btn-lg w-full bg-white text-wash hover:bg-gray-50">
                        Get Started
                    </a>
                </div>

                <div class="bg-white rounded-2xl p-8 border-2 border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Comforter</h3>
                    <p class="text-gray-600 mb-8">Blankets & bedding</p>
                    <div class="text-center py-12">
                        <span class="text-6xl font-black text-gray-900">₱200</span>
                        <p class="text-gray-600 mt-3 font-medium">per piece</p>
                    </div>
                    <p class="text-gray-600 text-center">Includes wash, dry, and fold for all sizes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24 bg-gradient-to-br from-wash to-wash-dark">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-5xl font-black text-white mb-6">Ready to Save Time on Laundry?</h2>
            <p class="text-xl text-white/90 mb-10">Join hundreds of happy customers who trust WashHour.</p>
            <a href="{{ route('user.register') }}" class="btn btn-lg bg-white text-wash hover:bg-gray-50 shadow-2xl">
                Start Your First Order — Free Pickup
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-12 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-white rounded-full p-2">
                            <img src="{{ asset('images/washhour_logo.png') }}" alt="Logo" class="h-8 w-8">
                        </div>
                        <span class="text-2xl font-black">WashHour</span>
                    </div>
                    <p class="text-gray-400">Your trusted laundry partner in Lipa City.</p>
                </div>

                <div>
                    <h4 class="font-bold mb-4 text-lg">Contact Us</h4>
                    <div class="space-y-3 text-gray-400">
                        <p class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            B6 L15 City Park Ave., Sabang, Lipa
                        </p>
                        <p class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            0921-776-9999
                        </p>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold mb-4 text-lg">Quick Links</h4>
                    <div class="space-y-2 text-gray-400">
                        <a href="{{ route('user.login') }}" class="block hover:text-white transition-colors">Login</a>
                        <a href="{{ route('user.register') }}" class="block hover:text-white transition-colors">Register</a>
                        <a href="#services" class="block hover:text-white transition-colors">Services</a>
                        <a href="#pricing" class="block hover:text-white transition-colors">Pricing</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center text-gray-500">
                © {{ date('Y') }} WashHour. All rights reserved.
            </div>
        </div>
    </footer>

    @push('scripts')
        @vite(['resources/js/pages/landing.js'])
    @endpush
</x-guest>
