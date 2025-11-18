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
        Product::create(
            [
                'product_name' => 'Detergent',
                'price' => 15.00
            ]
        );
        Product::create(
            [
                'product_name' => 'Fabric Softener',
                'price' => 10.00
            ]
        );
        Product::create(
            [
                'product_name' => 'Color Safe',
                'price' => 11.00
            ]
        );
    }
}
