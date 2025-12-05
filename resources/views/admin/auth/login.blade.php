<x-guest :showNav="true" :isAdmin="true">
    <x-slot:title>Admin Login</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 pt-20">
        <div class="w-full max-w-md">
            <!-- Content Card with Wash Accents -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-8 shadow-sm">
                <!-- Header with Wash Accent -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-wash/10 rounded-xl mb-4">
                        <svg class="w-8 h-8 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 mb-2">Admin Portal</h1>
                    <p class="text-sm text-gray-600">Sign in to your account</p>
                </div>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="alert alert-error mb-6">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('admin.login') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Username Input -->
                    <div class="form-group">
                        <label for="username" class="form-label">Username or Email <span class="text-error">*</span></label>
                        <input type="text" id="username" name="username" class="form-input" 
                            placeholder="Enter your username or email" required>
                    </div>
                    
                    <!-- Password Input -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password <span class="text-error">*</span></label>
                        <input type="password" id="password" name="password" class="form-input" 
                            placeholder="Enter your password" required>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="form-checkbox">
                        <label for="remember" class="form-check-label">Remember me</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-lg w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Sign In
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <p>
                        Forgot your password?
                        <a href="{{ route('admin.forgot-password') }}" class="text-wash hover:text-wash-dark font-bold hover:underline transition-colors">
                            Reset it here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest>
