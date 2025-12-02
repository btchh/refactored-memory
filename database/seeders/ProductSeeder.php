<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Products = Physical items/add-ons
     */
    public function run(): void
    {
        $admins = Admin::all();

        foreach ($admins as $index => $admin) {
            // Slight price variation for second branch
            $priceMultiplier = $index === 0 ? 1.0 : 1.1;

            // Clothes products (add-ons)
            Product::create([
                'admin_id' => $admin->id,
                'product_name' => 'Detergent',
                'price' => round(15.00 * $priceMultiplier, 2),
                'item_type' => 'clothes',
            ]);
            Product::create([
                'admin_id' => $admin->id,
                'product_name' => 'Fabric Conditioner',
                'price' => round(20.00 * $priceMultiplier, 2),
                'item_type' => 'clothes',
            ]);

            // Comforter products (add-ons)
            Product::create([
                'admin_id' => $admin->id,
                'product_name' => 'Safai',
                'price' => round(15.00 * $priceMultiplier, 2),
                'item_type' => 'comforter',
            ]);
            Product::create([
                'admin_id' => $admin->id,
                'product_name' => 'Color Protection',
                'price' => round(25.00 * $priceMultiplier, 2),
                'item_type' => 'comforter',
            ]);
            Product::create([
                'admin_id' => $admin->id,
                'product_name' => 'Packaging',
                'price' => round(20.00 * $priceMultiplier, 2),
                'item_type' => 'comforter',
            ]);

            // Shoes products (add-ons)
            Product::create([
                'admin_id' => $admin->id,
                'product_name' => 'Polish',
                'price' => round(20.00 * $priceMultiplier, 2),
                'item_type' => 'shoes',
            ]);
            Product::create([
                'admin_id' => $admin->id,
                'product_name' => 'Deodorize',
                'price' => round(15.00 * $priceMultiplier, 2),
                'item_type' => 'shoes',
            ]);
        }
    }
}
