<!-- Notifications Container -->
<div id="notifications-container" class="fixed top-4 right-4 z-50 space-y-3 pointer-events-none"></div>

@if (session('success') || session('error') || session('warning') || session('info'))
    <script>
        // Pass session messages to JavaScript
        window.sessionMessages = {
            @if(session('success'))
                success: @json(session('success')),
            @endif
            @if(session('error'))
                error: @json(session('error')),
            @endif
            @if(session('warning'))
                warning: @json(session('warning')),
            @endif
            @if(session('info'))
                info: @json(session('info')),
            @endif
        };
    </script>
@endif
