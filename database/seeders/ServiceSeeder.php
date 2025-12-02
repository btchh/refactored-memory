<?php

namespace Database\Seeders;

use App\Models\Admin;
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
        $admins = Admin::all();

        foreach ($admins as $index => $admin) {
            // Slight price variation for second branch
            $priceMultiplier = $index === 0 ? 1.0 : 1.1;

            // Clothes services
            Service::create([
                'admin_id' => $admin->id,
                'service_name' => 'Wash',
                'price' => round(70.00 * $priceMultiplier, 2),
                'item_type' => 'clothes',
                'is_bundle' => false,
            ]);
            Service::create([
                'admin_id' => $admin->id,
                'service_name' => 'Dry',
                'price' => round(70.00 * $priceMultiplier, 2),
                'item_type' => 'clothes',
                'is_bundle' => false,
            ]);
            Service::create([
                'admin_id' => $admin->id,
                'service_name' => 'Fold',
                'price' => round(20.00 * $priceMultiplier, 2),
                'item_type' => 'clothes',
                'is_bundle' => false,
            ]);
            Service::create([
                'admin_id' => $admin->id,
                'service_name' => 'Delivery',
                'price' => round(20.00 * $priceMultiplier, 2),
                'item_type' => 'clothes',
                'is_bundle' => false,
            ]);
            Service::create([
                'admin_id' => $admin->id,
                'service_name' => 'Per Load (With Fold)',
                'price' => round(165.00 * $priceMultiplier, 2),
                'item_type' => 'clothes',
                'is_bundle' => true,
                'bundle_items' => ['Wash', 'Dry', 'Detergent', 'Fabric Conditioner', 'Fold'],
            ]);
            Service::create([
                'admin_id' => $admin->id,
                'service_name' => 'Per Load (Without Fold)',
                'price' => round(175.00 * $priceMultiplier, 2),
                'item_type' => 'clothes',
                'is_bundle' => true,
                'bundle_items' => ['Wash', 'Dry', 'Detergent', 'Fabric Conditioner'],
            ]);

            // Comforter services
            Service::create([
                'admin_id' => $admin->id,
                'service_name' => 'Single Piece',
                'price' => round(200.00 * $priceMultiplier, 2),
                'item_type' => 'comforter',
                'is_bundle' => false,
            ]);

            // Shoes services
            Service::create([
                'admin_id' => $admin->id,
                'service_name' => 'Shoe Cleaning',
                'price' => round(50.00 * $priceMultiplier, 2),
                'item_type' => 'shoes',
                'is_bundle' => false,
            ]);
        }
    }
}
