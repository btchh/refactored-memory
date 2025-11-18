<!-- Notifications Container -->
<div id="notifications-container" class="fixed top-4 right-4 z-50 space-y-3 pointer-events-none"></div>

@if (session('success'))
    <script>
        function showSuccessNotification() {
            const manager = window.notificationManager;
            if (manager) {
                manager.success('{{ session('success') }}', 5000);
            } else {
                setTimeout(showSuccessNotification, 100);
            }
        }
        showSuccessNotification();
    </script>
@endif

@if (session('error'))
    <script>
        function showErrorNotification() {
            const manager = window.notificationManager;
            if (manager) {
                manager.error('{{ session('error') }}', 0);
            } else {
                setTimeout(showErrorNotification, 100);
            }
        }
        showErrorNotification();
    </script>
@endif

@if (session('warning'))
    <script>
        function showWarningNotification() {
            const manager = window.notificationManager;
            if (manager) {
                manager.warning('{{ session('warning') }}', 5000);
            } else {
                setTimeout(showWarningNotification, 100);
            }
        }
        showWarningNotification();
    </script>
@endif

@if (session('info'))
    <script>
        function showInfoNotification() {
            const manager = window.notificationManager;
            if (manager) {
                manager.info('{{ session('info') }}', 5000);
            } else {
                setTimeout(showInfoNotification, 100);
            }
        }
        showInfoNotification();
    </script>
@endif
