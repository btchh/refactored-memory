@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'outline' => false,
    'fullWidth' => false,
])

@php
    $baseClasses = 'btn inline-flex items-center justify-center font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    $variants = [
        'primary' => $outline 
            ? 'border-2 border-primary-600 text-primary-600 hover:bg-primary-50 focus:ring-primary-500' 
            : 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500',
        'secondary' => $outline 
            ? 'border-2 border-gray-600 text-gray-600 hover:bg-gray-50 focus:ring-gray-500' 
            : 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
        'success' => $outline 
            ? 'border-2 border-green-600 text-green-600 hover:bg-green-50 focus:ring-green-500' 
            : 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        'danger' => $outline 
            ? 'border-2 border-red-600 text-red-600 hover:bg-red-50 focus:ring-red-500' 
            : 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'warning' => $outline 
            ? 'border-2 border-yellow-500 text-yellow-600 hover:bg-yellow-50 focus:ring-yellow-400' 
            : 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-400',
        'info' => $outline 
            ? 'border-2 border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-500' 
            : 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'outline' => 'border-2 border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-gray-500',
    ];
    
    $sizes = [
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
    
    $classes = implode(' ', array_filter([
        $baseClasses,
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        $fullWidth ? 'w-full' : '',
    ]));
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
