@props([
    'id' => 'modal',
    'title' => '',
    'size' => 'md',
    'footer' => null,
])

@php
    $sizeClasses = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
    ];
    
    $modalSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div id="{{ $id }}" class="modal fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="{{ $id }}-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="modal-backdrop fixed inset-0 bg-black/50 transition-opacity duration-200" onclick="document.getElementById('{{ $id }}').classList.add('hidden')"></div>

        <!-- Modal panel -->
        <div class="modal-content inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle {{ $modalSize }} w-full animate-scale-in">
            <!-- Header -->
            @if($title)
                <div class="modal-header px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="modal-title text-xl font-semibold text-gray-900" id="{{ $id }}-title">
                        {{ $title }}
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 rounded transition-colors duration-200" onclick="document.getElementById('{{ $id }}').classList.add('hidden')" aria-label="Close modal">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Body -->
            <div class="modal-body px-6 py-6">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @if($footer)
                <div class="modal-footer px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal helpers loaded via app.js -->
