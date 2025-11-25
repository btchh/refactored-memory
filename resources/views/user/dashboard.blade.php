<x-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="space-y-8">
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center gap-4 mb-4">
                <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                    <span class="text-5xl">👋</span>
                </div>
                <div>
                    <h1 class="text-4xl font-bold mb-2">Welcome Back!</h1>
                    <p class="text-xl opacity-90">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
                </div>
            </div>
            <p class="text-lg opacity-90">Ready to make your laundry day easier? Let's get started!</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-5xl">📦</span>
                    <div class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <span class="text-2xl font-bold">0</span>
                    </div>
                </div>
                <h3 class="text-xl font-semibold mb-1">Active Orders</h3>
                <p class="text-sm opacity-90">Currently in progress</p>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-5xl">✅</span>
                    <div class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <span class="text-2xl font-bold">0</span>
                    </div>
                </div>
                <h3 class="text-xl font-semibold mb-1">Completed</h3>
                <p class="text-sm opacity-90">Total orders done</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white hover:scale-105 transition-transform duration-300">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-5xl">💰</span>
                    <div class="bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <span class="text-2xl font-bold">₱0</span>
                    </div>
                </div>
                <h3 class="text-xl font-semibold mb-1">Total Spent</h3>
                <p class="text-sm opacity-90">Lifetime spending</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('user.booking') }}" class="group">
                <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-transparent hover:border-blue-500 hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors">
                        <span class="text-4xl">🧺</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Book Laundry</h3>
                    <p class="text-sm text-gray-600">Schedule a new pickup</p>
                </div>
            </a>

            <a href="{{ route('user.status') }}" class="group">
                <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-transparent hover:border-green-500 hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                    <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mb-4 group-hover:bg-green-200 transition-colors">
                        <span class="text-4xl">📊</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Track Status</h3>
                    <p class="text-sm text-gray-600">Check order progress</p>
                </div>
            </a>

            <a href="{{ route('user.shop-location') }}" class="group">
                <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-transparent hover:border-orange-500 hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                    <div class="bg-orange-100 rounded-full w-16 h-16 flex items-center justify-center mb-4 group-hover:bg-orange-200 transition-colors">
                        <span class="text-4xl">📍</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Find Shops</h3>
                    <p class="text-sm text-gray-600">Locate nearby stores</p>
                </div>
            </a>

            <a href="{{ route('user.history') }}" class="group">
                <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-transparent hover:border-purple-500 hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                    <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mb-4 group-hover:bg-purple-200 transition-colors">
                        <span class="text-4xl">📜</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">History</h3>
                    <p class="text-sm text-gray-600">View past orders</p>
                </div>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-3xl">⏱️</span>
                <h2 class="text-2xl font-bold text-gray-800">Recent Activity</h2>
            </div>
            <div class="text-center py-12 text-gray-500">
                <span class="text-6xl mb-4 block">📭</span>
                <p class="text-lg">No recent activity yet</p>
                <p class="text-sm mt-2">Your bookings will appear here</p>
            </div>
        </div>
    </div>
</x-layout>
