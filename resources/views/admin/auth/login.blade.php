<x-guest :showNav="true" :isAdmin="true">
    <x-slot:title>Admin Login</x-slot:title>
    
    <div class="min-h-screen flex items-center justify-center p-4 pt-20 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-center bg-no-repeat" style="background-image: url('{{ asset('images/image.png') }}'); background-size: cover; background-position: center center; filter: blur(3px); transform: scale(1.1);">
            <!-- Overlay for better readability -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/15 via-gray-900/15 to-zinc-900/15"></div>
        </div>
        
        <!-- Fallback gradient if no image -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-white to-gray-50 -z-10"></div>
        
        <!-- Animated Background Elements (optional decorative elements) -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-slate-400/10 to-gray-400/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-gray-400/10 to-zinc-400/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="w-full max-w-md relative z-10">
            <x-modules.card class="p-8 bg-white/95 shadow-2xl border-0 card-entrance rounded-2xl">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-slate-600 to-gray-800 rounded-2xl mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Admin Portal</h1>
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
                <form action="{{ route('admin.login') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <x-modules.input type="text" name="username" label="Username or Email" 
                        placeholder="Enter your username or email" required />
                    
                    <x-modules.input type="password" name="password" label="Password" 
                        placeholder="Enter your password" required />

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" 
                            class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 focus:ring-2 cursor-pointer">
                        <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer select-none">Remember me</label>
                    </div>

                    <x-modules.button type="submit" variant="primary" :fullWidth="true" class="shadow-lg hover:shadow-xl transition-all duration-200">
                        Sign In
                    </x-modules.button>
                </form>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <p>
                        Forgot your password?
                        <a href="{{ route('admin.forgot-password') }}" class="text-primary-600 hover:text-primary-700 font-medium hover:underline">
                            Reset it here
                        </a>
                    </p>
                </div>
            </x-modules.card>
        </div>
    </div>
</x-guest>
