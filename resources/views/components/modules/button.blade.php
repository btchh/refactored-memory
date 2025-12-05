@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $variants = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'outline' => 'btn-outline',
        'ghost' => 'btn-ghost',
    ];
    
    $sizes = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
    ];
    
    $classes = 'btn ' . ($variants[$variant] ?? 'btn-primary') . ' ' . ($sizes[$size] ?? '');
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
