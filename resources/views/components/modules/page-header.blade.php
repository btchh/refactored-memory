@props([
    'title' => 'Page Title',
    'subtitle' => '',
    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
])

<div class="bg-gradient-to-r from-wash to-wash-dark rounded-xl p-8 text-white shadow-lg">
    <div class="flex items-center gap-5">
        <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-black mb-1">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-white/80 text-lg">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    
    {{ $slot }}
</div>
