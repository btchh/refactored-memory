<div id="cookie-consent" class="fixed bottom-0 inset-x-0 z-[100] p-4 hidden">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-2xl border border-gray-200 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <h3 class="font-semibold text-gray-900">We use cookies</h3>
                </div>
                <p class="text-sm text-gray-600">
                    We use essential cookies to keep you logged in and remember your session. No tracking, no ads — just what's needed to make the app work.
                </p>
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <button id="cookie-accept" class="flex-1 sm:flex-none px-5 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                    Got it
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const consent = localStorage.getItem('cookie_consent');
    const banner = document.getElementById('cookie-consent');
    
    if (!consent && banner) {
        banner.classList.remove('hidden');
    }
    
    document.getElementById('cookie-accept')?.addEventListener('click', function() {
        localStorage.setItem('cookie_consent', 'accepted');
        banner.classList.add('hidden');
    });
})();
</script>
