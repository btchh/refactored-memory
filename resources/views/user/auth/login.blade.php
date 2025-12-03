<x-guest :showNav="true">
    <x-slot:title>User Login</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 pt-20">
        <div class="w-full max-w-md">
            <x-modules.card class="p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">User Login</h1>
                    <p class="text-sm text-gray-600">Sign in to your account</p>
                </div>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <x-modules.alert type="error" class="mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-modules.alert>
                @endif

                <!-- Login Form -->
                <form action="{{ route('user.login') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <x-modules.input type="text" name="username" label="Username" 
                        placeholder="Enter your username" required />
                    
                    <x-modules.input type="password" name="password" label="Password" 
                        placeholder="Enter your password" required />

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" 
                            class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 focus:ring-2 cursor-pointer">
                        <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer select-none">Remember me</label>
                    </div>

                    <x-modules.button type="submit" variant="primary" :fullWidth="true">
                        Sign In
                    </x-modules.button>
                </form>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600 space-y-2">
                    <p>
                        Don't have an account?
                        <a href="{{ route('user.register') }}" class="text-primary-600 hover:text-primary-700 font-medium hover:underline">
                            Register here
                        </a>
                    </p>
                    <p>
                        Forgot your password?
                        <a href="{{ route('user.forgot-password') }}" class="text-primary-600 hover:text-primary-700 font-medium hover:underline">
                            Reset it here
                        </a>
                    </p>
                </div>
            </x-modules.card>
        </div>
    </div>

    <!-- Account Suspended/Disabled Modal -->
    @if(session('account_restricted') || (session('error') && (str_contains(session('error'), 'suspended') || str_contains(session('error'), 'disabled'))))
    <div id="account-status-modal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: flex;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Account Access Restricted</h3>
                        <p class="text-sm text-gray-500">Unable to login</p>
                    </div>
                </div>
                <div class="mb-6">
                    <p class="text-gray-800 mb-4 font-medium">{{ session('error') }}</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>Need help?</strong> Please contact our support team for assistance with your account.
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeModal()" class="w-full px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors">
                    I Understand
                </button>
            </div>
        </div>
    </div>

    <script>
        function closeModal() {
            const modal = document.getElementById('account-status-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
        
        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
        
        // Close modal when clicking outside
        document.getElementById('account-status-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
    @endif
</x-guest>
