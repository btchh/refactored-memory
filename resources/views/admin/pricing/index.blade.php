<x-layout>
    <x-slot name="title">Pricing Management</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="card p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Pricing Management</h1>
            <p class="text-gray-600">Manage your services and products pricing</p>
        </div>

        <!-- Alert Container -->
        <div id="alert-container"></div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="border-b border-gray-200 px-6">
                <nav class="flex gap-6">
                    <button type="button" data-tab="services" class="pricing-tab active py-4 text-sm font-medium border-b-2 border-primary-600 text-primary-600">
                        Services
                    </button>
                    <button type="button" data-tab="products" class="pricing-tab py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                        Products
                    </button>
                </nav>
            </div>

            <!-- Services Panel -->
            <div id="services-panel" class="pricing-panel p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Services</h2>
                    <button type="button" onclick="openServiceModal()" class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Service
                    </button>
                </div>

                @foreach(['clothes' => 'Clothes', 'comforter' => 'Comforter', 'shoes' => 'Shoes'] as $type => $label)
                    @php $typeServices = $services->where('item_type', $type); @endphp
                    @if($typeServices->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ $label }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($typeServices as $service)
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-primary-300 transition-colors">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-semibold text-gray-900">{{ $service->service_name }}</h4>
                                            <span class="text-lg font-bold text-green-600">₱{{ number_format($service->price, 2) }}</span>
                                        </div>
                                        @if($service->description)
                                            <p class="text-sm text-gray-500 mb-3">{{ $service->description }}</p>
                                        @endif
                                        <div class="flex gap-2">
                                            <button type="button" onclick='editService(@json($service))' class="text-sm text-primary-600 hover:text-primary-700 font-medium">Edit</button>
                                            <span class="text-gray-300">|</span>
                                            <button type="button" onclick="deleteService({{ $service->id }}, '{{ addslashes($service->service_name) }}')" class="text-sm text-red-600 hover:text-red-700 font-medium">Delete</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                @if($services->count() === 0)
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-gray-500 mb-4">No services yet</p>
                        <button type="button" onclick="openServiceModal()" class="btn btn-primary btn-sm">Add Your First Service</button>
                    </div>
                @endif
            </div>


            <!-- Products Panel -->
            <div id="products-panel" class="pricing-panel p-6 hidden">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Products</h2>
                    <button type="button" onclick="openProductModal()" class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Product
                    </button>
                </div>

                @foreach(['clothes' => 'Clothes', 'comforter' => 'Comforter', 'shoes' => 'Shoes'] as $type => $label)
                    @php $typeProducts = $products->where('item_type', $type); @endphp
                    @if($typeProducts->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ $label }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($typeProducts as $product)
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-primary-300 transition-colors">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-semibold text-gray-900">{{ $product->product_name }}</h4>
                                            <span class="text-lg font-bold text-green-600">₱{{ number_format($product->price, 2) }}</span>
                                        </div>
                                        @if($product->description)
                                            <p class="text-sm text-gray-500 mb-3">{{ $product->description }}</p>
                                        @endif
                                        <div class="flex gap-2">
                                            <button type="button" onclick='editProduct(@json($product))' class="text-sm text-primary-600 hover:text-primary-700 font-medium">Edit</button>
                                            <span class="text-gray-300">|</span>
                                            <button type="button" onclick="deleteProduct({{ $product->id }}, '{{ addslashes($product->product_name) }}')" class="text-sm text-red-600 hover:text-red-700 font-medium">Delete</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                @if($products->count() === 0)
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="text-gray-500 mb-4">No products yet</p>
                        <button type="button" onclick="openProductModal()" class="btn btn-primary btn-sm">Add Your First Product</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Service Modal -->
    <dialog id="service-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4" id="service-modal-title">Add Service</h3>
            <form id="service-form">
                <input type="hidden" id="service-id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Service Name</label>
                        <input type="text" id="service-name" class="form-input" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item Type</label>
                        <select id="service-item-type" class="form-select" required>
                            <option value="">Select type</option>
                            <option value="clothes">Clothes</option>
                            <option value="comforter">Comforter</option>
                            <option value="shoes">Shoes</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price (₱)</label>
                        <input type="number" id="service-price" class="form-input" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <textarea id="service-description" class="form-textarea" rows="2"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('service-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Product Modal -->
    <dialog id="product-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4" id="product-modal-title">Add Product</h3>
            <form id="product-form">
                <input type="hidden" id="product-id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                        <input type="text" id="product-name" class="form-input" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item Type</label>
                        <select id="product-item-type" class="form-select" required>
                            <option value="">Select type</option>
                            <option value="clothes">Clothes</option>
                            <option value="comforter">Comforter</option>
                            <option value="shoes">Shoes</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price (₱)</label>
                        <input type="number" id="product-price" class="form-input" step="0.01" min="0" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <textarea id="product-description" class="form-textarea" rows="2"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('product-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
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
