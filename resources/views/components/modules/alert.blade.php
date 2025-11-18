@props([
    'type' => 'info',
    'dismissible' => false,
    'icon' => null,
])

@php
    $typeClasses = [
        'success' => 'bg-green-50 border-green-200 text-green-800',
        'error' => 'bg-red-50 border-red-200 text-red-800',
        'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800',
    ];
    
    $iconDefaults = [
        'success' => '✓',
        'error' => '✕',
        'warning' => '⚠',
        'info' => 'ℹ',
    ];
    
    $classes = 'alert border-l-4 p-4 rounded-r-lg ' . ($typeClasses[$type] ?? $typeClasses['info']);
    $displayIcon = $icon ?? $iconDefaults[$type];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="alert">
    <div class="flex items-start">
        @if($displayIcon)
            <span class="flex-shrink-0 mr-3 text-xl">{{ $displayIcon }}</span>
        @endif
        
        <div class="flex-1">
            {{ $slot }}
        </div>
        
        @if($dismissible)
            <button type="button" class="flex-shrink-0 ml-3 text-gray-400 hover:text-gray-600" onclick="this.parentElement.parentElement.remove()">
                <span class="text-xl">&times;</span>
            </button>
        @endif
    </div>
</div>
