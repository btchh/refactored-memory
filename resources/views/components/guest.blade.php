<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'WashHour') }}{{ isset($title) ? ' | ' . $title : '' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @if(isset($showNav) && $showNav)
        <x-modules.guest-nav :isAdmin="$isAdmin ?? false" />
    @endif

    {{ $slot }}
    
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-[200] flex flex-col gap-3 max-w-sm w-full pointer-events-none"></div>

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

    @stack('scripts')
</body>
</html>
