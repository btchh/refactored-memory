@props([
    'title' => null,
    'footer' => null,
    'padding' => true,
])

@php
    $cardClasses = 'card bg-base-100 shadow-xl';
    $bodyClasses = $padding ? 'card-body' : '';
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}>
    @if($title)
        <div class="card-header px-6 py-4 border-b border-base-300 bg-base-200">
            <h3 class="card-title">{{ $title }}</h3>
        </div>
    @endif
    
    <div class="{{ $bodyClasses }}">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="card-footer px-6 py-4 border-t border-base-300 bg-base-200">
            {{ $footer }}
        </div>
    @endif
</div>
