<x-layout>
    <x-slot name="title">Pricing Management</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Pricing Management</h1>
                    <p class="text-gray-600 mt-1">Manage services and products pricing</p>
                </div>
            </div>
        </div>

        <!-- Alert Container -->
        <div id="alert-container"></div>

        <!-- Tabs -->
        <div class="card">
            <div class="border-b border-gray-200">
                <nav class="flex gap-4 px-6">
                    <button type="button" 
                            data-tab="services" 
                            class="pricing-tab active inline-flex items-center gap-2 px-4 py-4 text-sm font-medium border-b-2 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Services</span>
                    </button>
                    <button type="button" 
                            data-tab="products" 
                            class="pricing-tab inline-flex items-center gap-2 px-4 py-4 text-sm font-medium border-b-2 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Products</span>
                    </button>
                </nav>
            </div>

            <!-- Services Tab Content -->
            <div id="services-panel" class="pricing-panel p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Services List</h2>
                    <button type="button" onclick="openServiceModal()" class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Service
                    </button>
                </div>

                <div class="space-y-6">
                    @foreach(['clothes' => 'Clothes', 'comforter' => 'Comforter', 'shoes' => 'Shoes'] as $type => $label)
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $label }} Services</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Price</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($services->where('item_type', $type) as $service)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $service->service_name }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-700 font-semibold">₱{{ number_format($service->price, 2) }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-600">{{ $service->description ?? '-' }}</td>
                                                <td class="px-6 py-4 text-right space-x-2">
                                                    <button type="button" onclick='editService(@json($service))' class="btn btn-sm btn-outline">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button type="button" onclick="deleteService({{ $service->id }}, '{{ addslashes($service->service_name) }}')" class="btn btn-sm btn-error">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-12 text-center">
                                                    <div class="text-gray-400">
                                                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                        </svg>
                                                        <p class="text-sm font-medium">No services found for {{ $label }}</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Products Tab Content -->
            <div id="products-panel" class="pricing-panel p-6 hidden">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Products List</h2>
                    <button type="button" onclick="openProductModal()" class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Product
                    </button>
                </div>

                <div class="space-y-6">
                    @foreach(['clothes' => 'Clothes', 'comforter' => 'Comforter', 'shoes' => 'Shoes'] as $type => $label)
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $label }} Products</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Product Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Price</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($products->where('item_type', $type) as $product)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->product_name }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-700 font-semibold">₱{{ number_format($product->price, 2) }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-600">{{ $product->description ?? '-' }}</td>
                                                <td class="px-6 py-4 text-right space-x-2">
                                                    <button type="button" onclick='editProduct(@json($product))' class="btn btn-sm btn-outline">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button type="button" onclick="deleteProduct({{ $product->id }}, '{{ addslashes($product->product_name) }}')" class="btn btn-sm btn-error">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-12 text-center">
                                                    <div class="text-gray-400">
                                                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                        </svg>
                                                        <p class="text-sm font-medium">No products found for {{ $label }}</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Service Modal -->
    <dialog id="service-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-lg w-full">
            <form method="dialog" class="absolute top-4 right-4">
                <button type="submit" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </form>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-6" id="service-modal-title">Add Service</h3>
            
            <form id="service-form" class="space-y-4">
                <input type="hidden" id="service-id">
                
                <div class="form-group">
                    <label class="form-label" for="service-name">Service Name</label>
                    <input type="text" id="service-name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="service-item-type">Item Type</label>
                    <select id="service-item-type" class="form-select" required>
                        <option value="">Select type</option>
                        <option value="clothes">Clothes</option>
                        <option value="comforter">Comforter</option>
                        <option value="shoes">Shoes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="service-price">Price (₱)</label>
                    <input type="number" id="service-price" class="form-input" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="service-description">Description (Optional)</label>
                    <textarea id="service-description" class="form-textarea" rows="3"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('service-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Service</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Product Modal -->
    <dialog id="product-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-lg w-full">
            <form method="dialog" class="absolute top-4 right-4">
                <button type="submit" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </form>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-6" id="product-modal-title">Add Product</h3>
            
            <form id="product-form" class="space-y-4">
                <input type="hidden" id="product-id">
                
                <div class="form-group">
                    <label class="form-label" for="product-name">Product Name</label>
                    <input type="text" id="product-name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="product-item-type">Item Type</label>
                    <select id="product-item-type" class="form-select" required>
                        <option value="">Select type</option>
                        <option value="clothes">Clothes</option>
                        <option value="comforter">Comforter</option>
                        <option value="shoes">Shoes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="product-price">Price (₱)</label>
                    <input type="number" id="product-price" class="form-input" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="product-description">Description (Optional)</label>
                    <textarea id="product-description" class="form-textarea" rows="3"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('product-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </dialog>

    @push('scripts')
    <script>
        window.pricingRoutes = {
            services: {
                store: '{{ route('admin.pricing.services.store') }}',
                update: '{{ route('admin.pricing.services.update', ['id' => '__ID__']) }}',
                delete: '{{ route('admin.pricing.services.delete', ['id' => '__ID__']) }}'
            },
            products: {
                store: '{{ route('admin.pricing.products.store') }}',
                update: '{{ route('admin.pricing.products.update', ['id' => '__ID__']) }}',
                delete: '{{ route('admin.pricing.products.delete', ['id' => '__ID__']) }}'
            },
            csrf: '{{ csrf_token() }}'
        };
    </script>
    @vite(['resources/js/pages/admin-pricing.js'])
    @endpush
</x-layout>
