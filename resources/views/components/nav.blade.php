@props(['type' => 'admin'])

@php
    $config = config('navigation.' . $type);
    $colorClasses = [
        'blue' => 'from-blue-600 to-blue-800',
        'green' => 'from-green-600 to-green-800',
        'purple' => 'from-purple-600 to-purple-800',
    ];
    $bgClass = $colorClasses[$config['color']] ?? $colorClasses['blue'];
    $guard = $type === 'admin' ? 'admin' : 'web';
    $user = Auth::guard($guard)->user();
@endphp

<nav class="bg-gradient-to-r {{ $bgClass }} text-white shadow-lg">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-8">
            <h1 class="text-2xl font-bold">{{ $config['title'] }}</h1>
            <div class="hidden md:flex space-x-6">
                @foreach ($config['links'] as $link)
                    <a href="{{ route($link['route']) }}" class="hover:text-opacity-80 transition">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm">
                {{ $type === 'admin' ? $user->admin_name : $user->username }}
            </span>
            <form action="{{ route($type === 'admin' ? 'admin.logout' : 'user.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>
