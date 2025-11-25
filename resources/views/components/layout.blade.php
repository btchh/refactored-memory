<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'WashHour') }}{{ $title ? ' | ' . $title : '' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Prevent back button after logout
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
</head>
<body class="bg-gray-100">
    <!-- ✅ Top Navigation -->
    <x-nav type="user" />

    <!-- ✅ Page Layout -->
    <div class="flex min-h-screen pt-[6rem]">
        <!-- Sidebar -->
        <aside class="w-64 fixed top-[6rem] bottom-0 left-0 z-40 overflow-y-auto bg-white border-r border-gray-200">
            @include('components.modules.sidebar')
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-6">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
