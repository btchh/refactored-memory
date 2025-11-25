<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'WashHour') }}@isset($title) | {{ $title }}@endisset</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Prevent back button after logout
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                // Page was loaded from cache (back button)
                window.location.reload();
            }
        });
    </script>
    @stack('styles')
</head>
<body>
    <!-- Admin Navigation -->
    <x-nav type="admin" />

    <!-- Main Content -->
    <main class="main-content">
        <div class="container mx-auto px-4 py-8">
            <!-- Notifications -->
            <x-notifications />

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="alert alert-error mb-6">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
