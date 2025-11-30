<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PricingController extends Controller
{
    /**
     * Display pricing management page
     */
    public function index()
    {
        $services = Service::orderBy('item_type')->orderBy('service_name')->get();
        $products = Product::orderBy('item_type')->orderBy('product_name')->get();

        return view('admin.pricing.index', compact('services', 'products'));
    }

    /**
     * Store a new service
     */
    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'item_type' => 'required|in:clothes,comforter,shoes',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $service = Service::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'service' => $service,
            ]);
        } catch (\Exception $e) {
            Log::error('Service creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing service
     */
    public function updateService(Request $request, $id)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'item_type' => 'required|in:clothes,comforter,shoes',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $service = Service::findOrFail($id);
            $service->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'service' => $service,
            ]);
        } catch (\Exception $e) {
            Log::error('Service update failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a service
     */
    public function deleteService($id)
    {
        try {
            $service = Service::findOrFail($id);
            
            // Check if service is used in any transactions
            $usageCount = DB::table('service_transaction')->where('service_id', $id)->count();
            
            if ($usageCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete service. It is used in {$usageCount} booking(s).",
                ], 400);
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Service deletion failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new product
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'item_type' => 'required|in:clothes,comforter,shoes',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $product = Product::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            Log::error('Product creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing product
     */
    public function updateProduct(Request $request, $id)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'item_type' => 'required|in:clothes,comforter,shoes',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $product = Product::findOrFail($id);
            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            Log::error('Product update failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a product
     */
    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Check if product is used in any transactions
            $usageCount = DB::table('product_transaction')->where('product_id', $id)->count();
            
            if ($usageCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete product. It is used in {$usageCount} booking(s).",
                ], 400);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Product deletion failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage(),
            ], 500);
        }
    }
}
