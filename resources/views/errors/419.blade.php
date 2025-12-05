<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Hero-style Header -->
        <div class="relative bg-gradient-to-br from-wash via-wash-dark to-gray-900 rounded-2xl p-12 overflow-hidden mb-8">
            <!-- Decorative Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
            </div>
            
            <!-- Content -->
            <div class="relative text-center">
                <div class="mb-6">
                    <svg class="mx-auto h-20 w-20 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-7xl md:text-8xl font-black text-white mb-4">419</h1>
                <h2 class="text-2xl md:text-3xl font-black text-white mb-3">Session Expired</h2>
                <p class="text-lg text-white/80">Your session has expired. Please refresh and try again.</p>
            </div>
        </div>

        <!-- Navigation Button -->
        <div class="text-center">
            <a href="{{ url()->current() }}" class="btn btn-primary btn-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Page
            </a>
        </div>
    </div>
</body>
</html>
