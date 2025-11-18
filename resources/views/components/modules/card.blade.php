@props([
    'title' => null,
    'footer' => null,
    'padding' => true,
])

@php
    $cardClasses = 'card bg-white rounded-lg shadow-md overflow-hidden';
    $bodyClasses = $padding ? 'card-body p-6' : 'card-body';
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    @if($title)
        <div class="card-header px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
        </div>
    @endif
    
    <div class="{{ $bodyClasses }}">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="card-footer px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>
