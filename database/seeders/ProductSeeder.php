<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clothes products
        Product::create([
            'product_name' => 'Detergent',
            'price' => 15.00,
            'item_type' => 'clothes',
        ]);
        Product::create([
            'product_name' => 'Fabric Conditioner',
            'price' => 20.00,
            'item_type' => 'clothes',
        ]);

        // Comforter products
        Product::create([
            'product_name' => 'Safai',
            'price' => 15.00,
            'item_type' => 'comforter',
        ]);
        Product::create([
            'product_name' => 'Color Protection',
            'price' => 25.00,
            'item_type' => 'comforter',
        ]);
        Product::create([
            'product_name' => 'Packaging',
            'price' => 20.00,
            'item_type' => 'comforter',
        ]);

        // Shoes products
        Product::create([
            'product_name' => 'Polish',
            'price' => 20.00,
            'item_type' => 'shoes',
        ]);
        Product::create([
            'product_name' => 'Deodorize',
            'price' => 15.00,
            'item_type' => 'shoes',
        ]);
    }
}
