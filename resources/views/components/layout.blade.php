<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'WashHour') }}{{ isset($title) ? ' | ' . $title : '' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false }">
    <!-- Top Navigation -->
    @include('components.nav')

    <!-- Page Layout -->
    <div class="flex min-h-screen pt-16">
        <!-- Mobile/Tablet Menu Button -->
        <button @click="sidebarOpen = !sidebarOpen" 
                class="lg:hidden fixed bottom-6 right-6 z-[100] w-14 h-14 bg-primary-600 text-white rounded-full shadow-lg flex items-center justify-center hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                aria-label="Toggle menu">
            <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg x-show="sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Mobile/Tablet Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden fixed inset-0 bg-black/50 z-[95] top-16"
             style="display: none;"
             aria-hidden="true"></div>

        <!-- Sidebar -->
        <aside class="w-64 md:w-56 lg:w-64 fixed top-16 bottom-0 left-0 z-[96] overflow-y-auto bg-white border-r border-gray-200 transition-transform duration-300 lg:translate-x-0"
               :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
               x-cloak>
            @if(Auth::guard('admin')->check())
                @include('components.modules.admin-sidebar')
            @else
                @include('components.modules.sidebar')
            @endif
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64 md:ml-56 p-4 sm:p-6 md:p-7 lg:p-8 w-full">
            {{ $slot }}
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-20 right-4 z-[200] flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

    <!-- Session Flash Messages as Toasts -->
    @if(session('success') || session('error') || session('warning') || session('info'))
    <script>
        (function showSessionToasts() {
            function tryShowToasts() {
                if (window.Toast) {
                    @if(session('success'))
                        window.Toast.show({!! json_encode(session('success')) !!}, 'success');
                    @endif
                    @if(session('error'))
                        window.Toast.show({!! json_encode(session('error')) !!}, 'error');
                    @endif
                    @if(session('warning'))
                        window.Toast.show({!! json_encode(session('warning')) !!}, 'warning');
                    @endif
                    @if(session('info'))
                        window.Toast.show({!! json_encode(session('info')) !!}, 'info');
                    @endif
                } else {
                    // Retry if Toast not loaded yet
                    setTimeout(tryShowToasts, 100);
                }
            }
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(tryShowToasts, 200);
                });
            } else {
                setTimeout(tryShowToasts, 200);
            }
        })();
    </script>
    @endif

    <!-- Scripts Stack -->
    @stack('scripts')
    
    <!-- Alpine.js for mobile menu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
