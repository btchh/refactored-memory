@props([
    'type' => 'info',
    'dismissible' => true,
])

@php
    // WashHour Design System - Alert styling with proper badge colors and consistent spacing
    $types = [
        'success' => 'alert-success',
        'error' => 'alert-error',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
    ];
    
    $icons = [
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
        'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ];
    
    // Icon container colors matching badge system
    $iconColors = [
        'success' => 'bg-success/10 text-success',
        'error' => 'bg-error/10 text-error',
        'warning' => 'bg-warning/10 text-warning',
        'info' => 'bg-info/10 text-info',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'alert ' . ($types[$type] ?? $types['info'])]) }} role="alert">
    <!-- Icon Container with badge-style background -->
    <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $iconColors[$type] ?? $iconColors['info'] }} flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $icons[$type] ?? $icons['info'] !!}
        </svg>
    </div>
    
    <!-- Message Content -->
    <div class="flex-1 text-gray-900 font-medium">{{ $slot }}</div>
    
    <!-- Dismiss Button -->
    @if($dismissible)
        <button type="button" onclick="this.parentElement.remove()" class="flex-shrink-0 w-8 h-8 rounded-lg hover:bg-gray-900/5 flex items-center justify-center text-gray-600 hover:text-gray-900 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
