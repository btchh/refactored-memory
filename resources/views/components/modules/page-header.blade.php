{{--
    Page Header Component
    A gradient header with icon, title, subtitle, and optional stats/actions
    
    Usage:
    <x-modules.page-header
        title="Page Title"
        subtitle="Page description"
        icon="M9 12h6m-6 4h6m2 5H7..."
        gradient="slate"
    >
        <x-slot name="stats">
            <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2">
                <p class="text-white/70 text-xs">Total</p>
                <p class="text-xl font-bold">100</p>
            </div>
        </x-slot>
        <x-slot name="actions">
            <a href="#" class="btn btn-white">Export</a>
        </x-slot>
    </x-modules.page-header>
--}}

@props([
    'title' => 'Page Title',
    'subtitle' => '',
    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'gradient' => 'primary',
])

@php
    $gradients = [
        'primary' => 'from-primary-600 to-primary-700',
        'slate' => 'from-slate-700 to-slate-800',
        'blue' => 'from-blue-600 to-blue-700',
        'emerald' => 'from-emerald-600 to-emerald-700',
        'violet' => 'from-violet-600 to-violet-700',
        'rose' => 'from-rose-600 to-rose-700',
        'amber' => 'from-amber-500 to-amber-600',
        'indigo' => 'from-indigo-600 to-indigo-700',
        'cyan' => 'from-cyan-600 to-cyan-700',
        'teal' => 'from-teal-600 to-teal-700',
    ];
    $gradientClass = $gradients[$gradient] ?? $gradients['primary'];
@endphp

<div class="bg-gradient-to-r {{ $gradientClass }} rounded-2xl p-6 md:p-8 text-white print:hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="bg-white/10 backdrop-blur rounded-xl p-3 md:p-4">
                <svg class="w-7 h-7 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-bold">{{ $title }}</h1>
                @if($subtitle)
                    <p class="text-white/70 text-sm md:text-base">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        
        @if(isset($actions) || isset($stats))
            <div class="flex flex-wrap items-center gap-3">
                {{ $stats ?? '' }}
                {{ $actions ?? '' }}
            </div>
        @endif
    </div>
    
    {{ $slot }}
</div>
