<div id="cookie-consent" class="fixed bottom-0 inset-x-0 z-[100] p-4 hidden">
    <!-- WashHour Design System - Cookie consent with wash accent colors and consistent spacing -->
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-2xl border-2 border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
            <div class="flex-1 space-y-3">
                <div class="flex items-center gap-3">
                    <!-- Icon container with wash accent -->
                    <div class="w-12 h-12 bg-wash/10 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-gray-900">We use cookies</h3>
                </div>
                <p class="text-sm text-gray-600 font-medium leading-relaxed">
                    We use essential cookies to keep you logged in and remember your session. No tracking, no ads — just what's needed to make the app work.
                </p>
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <button id="cookie-accept" class="btn btn-primary flex-1 sm:flex-none whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
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
