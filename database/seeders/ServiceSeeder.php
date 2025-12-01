<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Services = Actions/Labor performed on items
     */
    public function run(): void
    {
        // Clothes services (actions only)
        Service::create([
            'service_name' => 'Wash',
            'price' => 70.00,
            'item_type' => 'clothes',
            'is_bundle' => false,
        ]);
        Service::create([
            'service_name' => 'Dry',
            'price' => 70.00,
            'item_type' => 'clothes',
            'is_bundle' => false,
        ]);
        Service::create([
            'service_name' => 'Fold',
            'price' => 20.00,
            'item_type' => 'clothes',
            'is_bundle' => false,
        ]);
        Service::create([
            'service_name' => 'Delivery',
            'price' => 20.00,
            'item_type' => 'clothes',
            'is_bundle' => false,
        ]);
        Service::create([
            'service_name' => 'Per Load (With Fold)',
            'price' => 165.00,
            'item_type' => 'clothes',
            'is_bundle' => true,
            'bundle_items' => ['Wash', 'Dry', 'Detergent', 'Fabric Conditioner', 'Fold'],
        ]);
        Service::create([
            'service_name' => 'Per Load (Without Fold)',
            'price' => 175.00,
            'item_type' => 'clothes',
            'is_bundle' => true,
            'bundle_items' => ['Wash', 'Dry', 'Detergent', 'Fabric Conditioner'],
        ]);

        // Comforter services (actions only)
        Service::create([
            'service_name' => 'Single Piece',
            'price' => 200.00,
            'item_type' => 'comforter',
            'is_bundle' => false,
        ]);

        // Shoes services (actions only)
        Service::create([
            'service_name' => 'Shoe Cleaning',
            'price' => 50.00,
            'item_type' => 'shoes',
            'is_bundle' => false,
        ]);
    }
}
