<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefreshServicesProductsSeeder extends Seeder
{
    /**
     * Clear and reseed services and products tables
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables
        Service::truncate();
        Product::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Run the seeders
        $this->call([
            ServiceSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
