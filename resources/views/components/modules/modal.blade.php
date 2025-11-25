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
        <div class="modal-overlay fixed inset-0 bg-gray-500/75 transition-opacity" onclick="document.getElementById('{{ $id }}').classList.add('hidden')"></div>

        <!-- Modal panel -->
        <div class="modal-panel inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle {{ $modalSize }} w-full">
            <!-- Header -->
            @if($title)
                <div class="modal-header px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900" id="{{ $id }}-title">
                        {{ $title }}
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="document.getElementById('{{ $id }}').classList.add('hidden')">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Body -->
            <div class="modal-body px-6 py-4">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @if($footer)
                <div class="modal-footer px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Helper functions to show/hide modal
    window.showModal = function(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }
    
    window.hideModal = function(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
