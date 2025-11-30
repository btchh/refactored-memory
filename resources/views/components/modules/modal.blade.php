@props([
    'id' => 'modal',
    'title' => '',
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
    ];
    $modalSize = $sizes[$size] ?? $sizes['md'];
@endphp

<dialog id="{{ $id }}" class="modal p-0 rounded-lg shadow-xl {{ $modalSize }} w-full backdrop:bg-black/50">
    <div class="modal-box bg-white rounded-lg {{ $modalSize }} w-full">
        @if($title)
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">{{ $title }}</h3>
                <form method="dialog">
                    <button type="submit" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </form>
            </div>
        @endif
        
        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
