<x-layout>
    <x-slot name="title">Booking Receipt</x-slot>

    <div class="max-w-3xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg overflow-hidden border border-gray-200">
            <!-- Header -->
            <div class="bg-primary-600 text-white p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold mb-2">Booking Receipt</h1>
                <p class="text-primary-100">Thank you for your booking!</p>
            </div>

            <!-- Receipt Content -->
            <div class="p-8 space-y-6">
                <!-- Booking Reference -->
                <div class="text-center pb-6 border-b-2 border-dashed">
                    <p class="text-sm text-gray-600 mb-1">Booking Reference</p>
                    <p class="text-2xl font-bold text-gray-800">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>

                <!-- Customer Info -->
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Customer Information
                    </h2>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                        <p><span class="font-semibold">Name:</span> {{ $transaction->user->fname }} {{ $transaction->user->lname }}</p>
                        <p><span class="font-semibold">Email:</span> {{ $transaction->user->email }}</p>
                        <p><span class="font-semibold">Phone:</span> {{ $transaction->user->phone }}</p>
                    </div>
                </div>

                <!-- Booking Details -->
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Booking Details
                    </h2>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                        <p><span class="font-semibold">Date:</span> {{ $transaction->formatted_date }}</p>
                        <p><span class="font-semibold">Time:</span> {{ $transaction->formatted_time }}</p>
                        <p><span class="font-semibold">Item Type:</span> {{ ucfirst($transaction->item_type) }}</p>
                        <p><span class="font-semibold">Pickup Address:</span> {{ $transaction->pickup_address }}</p>
                        @if($transaction->notes)
                            <p><span class="font-semibold">Notes:</span> {{ $transaction->notes }}</p>
                        @endif
                    </div>
                </div>

                <!-- Services -->
                @if($transaction->services->count() > 0)
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Services
                    </h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-2 font-semibold text-gray-700">Service</th>
                                    <th class="text-right py-2 font-semibold text-gray-700">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->services as $service)
                                <tr class="border-b border-gray-200 last:border-b-0">
                                    <td class="py-2 text-gray-800">{{ $service->service_name }}</td>
                                    <td class="text-right py-2 text-gray-800">₱{{ number_format($service->pivot->price_at_purchase, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Products -->
                @if($transaction->products->count() > 0)
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Products
                    </h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-2 font-semibold text-gray-700">Product</th>
                                    <th class="text-right py-2 font-semibold text-gray-700">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->products as $product)
                                <tr class="border-b border-gray-200 last:border-b-0">
                                    <td class="py-2 text-gray-800">{{ $product->product_name }}</td>
                                    <td class="text-right py-2 text-gray-800">₱{{ number_format($product->pivot->price_at_purchase, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Status -->
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Status
                    </h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        @php
                            $statusClasses = [
                                'completed' => 'bg-green-100 text-green-700',
                                'in_progress' => 'bg-blue-100 text-blue-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                'pending' => 'bg-yellow-100 text-yellow-700'
                            ];
                            $statusClass = $statusClasses[$transaction->status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full font-semibold text-xs uppercase tracking-wide {{ $statusClass }}">
                            {{ ucfirst(str_replace('_', ' ', $transaction->status)) }}
                        </span>
                    </div>
                </div>

                <!-- Total -->
                <div class="pt-6 border-t-2 border-dashed border-gray-300">
                    <div class="bg-green-50 rounded-lg p-6 border-l-4 border-success">
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-gray-900">Total Amount:</span>
                            <span class="text-4xl font-bold text-success">₱{{ number_format($transaction->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Booking Date -->
                <div class="text-center text-sm text-gray-500">
                    <p>Booked on {{ $transaction->created_at->format('F d, Y \a\t g:i A') }}</p>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 pt-4">
                    <a href="{{ route('user.booking') }}" class="btn btn-outline flex-1">Book Another</a>
                    <button onclick="window.print()" class="btn btn-primary flex-1">Print Receipt</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            .btn, nav, footer {
                display: none !important;
            }
        }
    </style>
    @endpush
</x-layout>
