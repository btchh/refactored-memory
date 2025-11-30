<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PricingController;

Route::middleware(['auth:admin', 'prevent.back'])->group(function () {
    // Pricing Management
    Route::get('pricing', [PricingController::class, 'index'])->name('pricing.index');
    
    // Services
    Route::post('pricing/services', [PricingController::class, 'storeService'])->name('pricing.services.store');
    Route::put('pricing/services/{id}', [PricingController::class, 'updateService'])->name('pricing.services.update');
    Route::delete('pricing/services/{id}', [PricingController::class, 'deleteService'])->name('pricing.services.delete');
    
    // Products
    Route::post('pricing/products', [PricingController::class, 'storeProduct'])->name('pricing.products.store');
    Route::put('pricing/products/{id}', [PricingController::class, 'updateProduct'])->name('pricing.products.update');
    Route::delete('pricing/products/{id}', [PricingController::class, 'deleteProduct'])->name('pricing.products.delete');
});
