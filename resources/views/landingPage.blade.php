<x-layout>
    <x-slot:title>
        Landing Page
    </x-slot:title>
    
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
            <button id="menu-toggle" class="text-gray-700 focus:outline-none">
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
        <ul class="flex flex-col space-y-4 p-6 text-right max-w-screen-lg mx-auto px-4 sm:px-6 lg:px-8">
            <li><a href="#home" class="nav-link text-gray-700 hover:text-blue-600">Home</a></li>
            <li><a href="#about" class="nav-link text-gray-700 hover:text-blue-600">About Us</a></li>
            <li><a href="#services" class="nav-link text-gray-700 hover:text-blue-600">Services</a></li>
            <li><a href="#products" class="nav-link text-gray-700 hover:text-blue-600">Products</a></li>
            <li><a href="#pricing" class="nav-link text-gray-700 hover:text-blue-600">Prices</a></li>
            <li class="flex justify-end items-center space-x-2">
            <a href="{{ route('user.login') }}" class="nav-link text-gray-700 hover:text-blue-600">Login</a>
            <span class="text-gray-400">|</span>
            <a href="{{ route('user.register') }}" class="nav-link text-gray-700 hover:text-blue-600">Register</a>
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
                <div class="absolute inset-0 bg-black/30 z-10"></div>

                <div class="relative z-20 w-full max-w-2xl px-8">
                    <h1 class="text-6xl font-bold mb-6 drop-shadow-lg">Wash Hour</h1>
                    <p class="text-xl mb-4">Where Clean Meets Smart, and Service Comes First.</p>
                    <p class="text-md mb-6">Experience hassle-free laundry booking, real-time updates, and eco-friendly service — all in one place.</p>
                    <a href="{{ route('user.booking') }}"
                       class="inline-block bg-[#40e0d0] text-black font-semibold px-6 py-3 rounded-full shadow-md hover:bg-[#2bc4b0] transition duration-300">
                        Book Now
                    </a>
                </div>
            </section>


            <!-- ABOUT SECTION -->
            <section id="about" class="py-8 px-6 scroll-mt-[100px] text-white" style="background: linear-gradient(to right, #0047ab, #1ca9c9);">
                <div class="text-center mb-4">
                    <h1 class="text-4xl font-bold text-yellow-300 tracking-wide">About Us</h1>
                </div>

                <div class="max-w-6xl mx-auto bg-white/10 backdrop-blur-md rounded-xl p-6 shadow-lg">
                    <div class="flex flex-col md:flex-row items-center gap-12">
                        <!-- Left Side: Image -->
                        <div class="md:w-1/2 w-full">
                            <img src="{{ asset('images/laundry-img.jpg') }}" alt="Wash Hour Care"
                                class="rounded-lg shadow-lg w-full max-w-md h-auto object-cover">
                        </div>

                        <!-- Right Side: Text Content -->
                        <div class="md:w-1/2 w-full space-y-6">
                            <div>
                                <h2 class="text-3xl font-bold text-yellow-300 mb-2">Your Smart Laundry Partner</h2>
                                <p class="text-white">
                                    Wash Hour is built for modern living — combining professional care with smart scheduling, real-time updates, and location-aware delivery. We make laundry effortless, secure, and always on time.
                                </p>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-yellow-300 mb-1">Personalized Support</h3>
                                <p class="text-white">
                                    Whether you're booking online or walking into our shop — our team is always ready to assist. We value your time and ensure that every laundry concern is handled with care, attention, and a smile.
                                </p>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-yellow-300 mb-1">Quality Care</h3>
                                <p class="text-white">
                                    We handle your garments with precision — separating whites, colors, and delicates. Our detergents are gentle yet effective, preserving fabric quality.
                                </p>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-yellow-300 mb-1">Seamless Booking</h3>
                                <p class="text-white">
                                    Book your laundry slot through our platform. With Google Calendar integration, your schedule is synced and stress-free.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


           <!-- SERVICES SECTION -->
            <section id="services" class="bg-gray-50 py-12 text-center w-full px-6 scroll-mt-24">
                <h2 class="text-3xl font-semibold text-gray-800 mb-8">Our Services</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
                    
                    <!-- Wash Card -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 transform hover:-translate-y-1 overflow-hidden">
                        <div class="h-2 bg-yellow-400"></div> <!-- Gold accent -->
                        <div class="px-6 py-6 flex flex-col items-center">
                            <img src="{{ asset('images/wash.jpg') }}" alt="Wash Service" class="w-44 h-36 object-cover rounded-md mb-4 mt-2">
                            <h3 class="text-blue-600 text-xl font-semibold mb-2">Wash</h3>
                            <p class="text-gray-600 text-sm text-center">Gentle and thorough cleaning using premium detergents. We separate whites, colors, and delicates to protect every fabric.</p>
                        </div>
                    </div>

                    <!-- Dry Card -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 transform hover:-translate-y-1 overflow-hidden">
                        <div class="h-2 bg-yellow-400"></div>
                        <div class="px-6 py-6 flex flex-col items-center">
                            <img src="{{ asset('images/dry.jpg') }}" alt="Dry Service" class="w-44 h-36 object-cover rounded-md mb-4 mt-2">
                            <h3 class="text-blue-600 text-xl font-semibold mb-2">Dry</h3>
                            <p class="text-gray-600 text-sm text-center">Fast and efficient tumble drying with temperature control to preserve softness and prevent shrinkage.</p>
                        </div>
                    </div>

                    <!-- Fold Card -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 transform hover:-translate-y-1 overflow-hidden">
                        <div class="h-2 bg-yellow-400"></div>
                        <div class="px-6 py-6 flex flex-col items-center">
                            <img src="{{ asset('images/fold.jpg') }}" alt="Fold Service" class="w-44 h-36 object-cover rounded-md mb-4 mt-2">
                            <h3 class="text-blue-600 text-xl font-semibold mb-2">Fold</h3>
                            <p class="text-gray-600 text-sm text-center">Neatly folded garments, sorted and packed with care — ready for pickup or delivery.</p>
                        </div>
                    </div>

                    <!-- Delivery Card -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 transform hover:-translate-y-1 overflow-hidden">
                        <div class="h-2 bg-yellow-400"></div>
                        <div class="px-6 py-6 flex flex-col items-center">
                            <img src="{{ asset('images/delivery.jpg') }}" alt="Delivery Service" class="w-44 h-36 object-cover rounded-md mb-4 mt-2">
                            <h3 class="text-blue-600 text-xl font-semibold mb-2">Delivery</h3>
                            <p class="text-gray-600 text-sm text-center">Convenient door-to-door service. Track your laundry in real-time and receive it fresh and on time.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- PRODUCTS SECTION -->
            <section id="products" class="bg-gray-50 py-12 text-center w-full px-6 scroll-mt-24">
                <h2 class="text-3xl font-semibold text-gray-800 mb-8">Our Products</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    
                    <!-- Detergents Card -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 transform hover:-translate-y-1 overflow-hidden">
                        <div class="h-2 bg-yellow-400"></div>
                        <div class="px-6 py-6 flex flex-col items-center">
                            <img src="{{ asset('images/detergent.jpg') }}" alt="Detergents" class="w-44 h-36 object-cover rounded-md mb-4 mt-2">
                            <h3 class="text-blue-600 text-xl font-semibold mb-2">Detergents</h3>
                            <p class="text-gray-600 text-sm text-center">Premium detergents that clean deeply while protecting fabric quality.</p>
                        </div>
                    </div>

                    <!-- Fabcon Card -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 transform hover:-translate-y-1 overflow-hidden">
                        <div class="h-2 bg-yellow-400"></div>
                        <div class="px-6 py-6 flex flex-col items-center">
                            <img src="{{ asset('images/fabcon.jpg') }}" alt="Fabric Conditioner" class="w-44 h-36 object-cover rounded-md mb-4 mt-2">
                            <h3 class="text-blue-600 text-xl font-semibold mb-2">Fabcon</h3>
                            <p class="text-gray-600 text-sm text-center">Fabric conditioner that keeps clothes soft, fresh, and easy to iron.</p>
                        </div>
                    </div>

                    <!-- Downy Card -->
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 transform hover:-translate-y-1 overflow-hidden">
                        <div class="h-2 bg-yellow-400"></div>
                        <div class="px-6 py-6 flex flex-col items-center">
                            <img src="{{ asset('images/downy.jpg') }}" alt="Downy" class="w-44 h-36 object-cover rounded-md mb-4 mt-2">
                            <h3 class="text-blue-600 text-xl font-semibold mb-2">Downy</h3>
                            <p class="text-gray-600 text-sm text-center">Trusted Downy products for long-lasting fragrance and fabric care.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- PRICING SECTION -->
            <section id="pricing" class="relative py-12 text-center w-full px-6 scroll-mt-24 overflow-hidden">
                <!-- Background image -->
                <img src="{{ asset('images/price-bg.jpg') }}" alt="Price Background"
                    class="absolute inset-0 w-full h-full object-cover z-0" />
                <!-- Overlay for readability -->
                <div class="absolute inset-0 bg-black/30 z-0"></div>

                <!-- Content -->
                <div class="relative z-10 max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 px-4 md:px-0">
                    
                    <!-- Basic Services -->
                    <div class="card bg-white shadow-lg rounded-xl overflow-hidden">
                        <div class="h-2 bg-yellow-400"></div>
                        <div class="card-body p-6 text-left">
                            <h3 class="text-xl font-semibold text-blue-600 mb-4">Basic Services</h3>
                            <ul class="space-y-2 text-sm text-gray-800">
                                <li class="flex justify-between"><span>Wash</span><span>₱70</span></li>
                                <li class="flex justify-between"><span>Dry</span><span>₱70</span></li>
                                <li class="flex justify-between"><span>Detergent</span><span>₱15</span></li>
                                <li class="flex justify-between"><span>Fabric Conditioner</span><span>₱20</span></li>
                                <li class="flex justify-between"><span>Fold</span><span>₱20</span></li>
                                <li class="flex justify-between"><span>Delivery</span><span>₱20</span></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Comforter -->
                    <div class="card bg-white shadow-lg rounded-xl overflow-hidden">
                        <div class="h-2 bg-yellow-400"></div>
                        <div class="card-body p-6 text-left">
                            <h3 class="text-xl font-semibold text-blue-600 mb-4">Comforter</h3>
                            <ul class="space-y-2 text-sm text-gray-800">
                                <li class="flex justify-between"><span>Single Piece</span><span>₱200</span></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Per Load -->
                    <div class="card bg-white shadow-lg rounded-xl overflow-hidden">
                        <div class="h-2 bg-yellow-400"></div>
                        <div class="card-body p-6 text-left">
                            <h3 class="text-xl font-semibold text-blue-600 mb-4">Per Load (7 kg)</h3>
                            <ul class="space-y-2 text-sm text-gray-800">
                                <li><strong>Minimum Load:</strong> 7 kg</li>
                                <li class="flex justify-between"><span>With Fold</span><span>₱195</span></li>
                                <li class="flex justify-between"><span>Without Fold</span><span>₱175</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- footer -->
        <footer class="bg-gray-800 text-white py-8">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                    <!-- Left: Logo + Brand + Contact Info -->
                    <div class="flex flex-col items-center md:items-start text-left space-y-4">
                        <!-- Logo + Brand -->
                        <a href="{{ url('/') }}" class="flex items-center gap-2 group" aria-label="Go to Home">
                            <img src="{{ asset('images/washhour_logo.png') }}" alt="WashHour Logo" class="h-10 w-10">
                            <span class="text-xl sm:text-2xl font-bold text-blue-400 group-hover:text-blue-300 transition-colors">
                                WashHour
                            </span>
                        </a>

                        <!-- Location -->
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/location.png') }}" alt="Location Icon" class="w-5 h-5">
                            <span class="text-sm md:text-base">
                                B6 L15 CITY PARK AVE., CITY PARK SUBDIVISION, SABANG LIPA
                            </span>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/phone.png') }}" alt="Phone Icon" class="w-5 h-5">
                            <span class="text-sm md:text-base">0921-776-9999</span>
                        </div>
                    </div>

                    <!-- Right: Copyright (centered vertically) -->
                    <div class="flex items-center justify-end text-gray-400 text-sm h-full">
                        &copy; {{ date('Y') }} WashHour. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>

  
    <!-- Scripts -->
<script>
  const toggle = document.getElementById('menu-toggle');
  const menu = document.getElementById('mobile-menu');
  const navLinks = document.querySelectorAll('.nav-link');

  toggle.addEventListener('click', () => menu.classList.toggle('hidden'));
  menu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => menu.classList.add('hidden')));

  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      navLinks.forEach(l => l.classList.remove('text-blue-600', 'font-bold'));
      link.classList.add('text-blue-600', 'font-bold');
    });
  });

  const sections = document.querySelectorAll("section[id]");
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        navLinks.forEach(l => l.classList.remove('text-blue-600', 'font-bold'));
        const activeLink = document.querySelector(.nav-link[href="#${entry.target.id}"]);
        if (activeLink) activeLink.classList.add('text-blue-600', 'font-bold');
      }
    });
  }, { threshold: 0.6 });
  sections.forEach(section => observer.observe(section));
</script>

</x-layout>