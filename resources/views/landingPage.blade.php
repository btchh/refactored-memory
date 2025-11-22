<x-layout>
    <x-slot:title>Landing Page</x-slot:title>

    <div class="min-h-screen flex flex-col">
        <header class="bg-blue-600 text-white p-4 flex justify-between items-center">
            <h1 class="text-3xl font-bold">Placeholder Landing page</h1>
            <nav class="flex gap-4">
                <a href="{{ route('admin.login') }}" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-100 transition">Admin Login</a>
                <a href="{{ route('user.login') }}" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-100 transition">User Login</a>
                <a href="{{ route('user.register') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">Register</a>
            </nav>
        </header>

        <main class="grow container mx-auto p-4">
            <section class="text-center my-8">
                <h2 class="text-2xl font-semibold mb-4">Discover Our Features</h2>
                <p class="mb-6">Explore the amazing features we offer to make your experience unforgettable.</p>
                <a href="#features" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Learn More</a>
            </section>

            <section id="features" class="my-8">
                <h2 class="text-2xl font-semibold mb-4 text-center">Features</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="border p-4 rounded shadow hover:shadow-lg transition">
                        <h3 class="font-bold mb-2 text-blue-600">Admin Dashboard</h3>
                        <p>Comprehensive admin panel to manage users, view analytics, and track locations in real-time.</p>
                        <a href="{{ route('admin.login') }}" class="text-blue-600 hover:underline mt-2 inline-block">Access Admin →</a>
                    </div>
                    <div class="border p-4 rounded shadow hover:shadow-lg transition">
                        <h3 class="font-bold mb-2 text-blue-600">User Dashboard</h3>
                        <p>Personal dashboard for users to manage their profile, track admins, and access all features.</p>
                        <a href="{{ route('user.login') }}" class="text-blue-600 hover:underline mt-2 inline-block">User Login →</a>
                    </div>
                    <div class="border p-4 rounded shadow hover:shadow-lg transition">
                        <h3 class="font-bold mb-2 text-blue-600">Real-Time Tracking</h3>
                        <p>Track admin locations and get route information with our advanced tracking system.</p>
                        <a href="{{ route('user.register') }}" class="text-blue-600 hover:underline mt-2 inline-block">Get Started →</a>
                    </div>
                </div>
            </section>

            <section class="my-12 bg-gray-100 p-8 rounded">
                <h2 class="text-2xl font-semibold mb-6 text-center">Get Started Today</h2>
                <div class="flex flex-col md:flex-row gap-6 justify-center items-center">
                    <div class="text-center">
                        <h3 class="font-bold mb-2">New User?</h3>
                        <p class="mb-4">Create an account to access all features</p>
                        <a href="{{ route('user.register') }}" class="bg-green-500 text-white px-6 py-3 rounded hover:bg-green-600 transition inline-block">Sign Up Now</a>
                    </div>
                    <div class="text-center">
                        <h3 class="font-bold mb-2">Already Have an Account?</h3>
                        <p class="mb-4">Login to continue your journey</p>
                        <a href="{{ route('user.login') }}" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition inline-block">Login</a>
                    </div>
                </div>
            </section>

            <section class="my-12">
                <h2 class="text-2xl font-semibold mb-6 text-center">Need Help?</h2>
                <div class="flex flex-col md:flex-row gap-6 justify-center">
                    <a href="{{ route('user.forgot-password') }}" class="text-center border p-4 rounded hover:shadow-lg transition">
                        <h3 class="font-bold mb-2">Forgot Password?</h3>
                        <p class="text-sm text-gray-600">Reset your password easily</p>
                    </a>
                    <a href="{{ route('admin.forgot-password') }}" class="text-center border p-4 rounded hover:shadow-lg transition">
                        <h3 class="font-bold mb-2">Admin Password Reset</h3>
                        <p class="text-sm text-gray-600">Admin password recovery</p>
                    </a>
                </div>
            </section>
        </main>

        <footer class="bg-gray-800 text-white p-4 text-center">
            &copy; {{ date('Y') }} Your Company. All rights reserved.
        </footer>
    </div>
</x-layout>