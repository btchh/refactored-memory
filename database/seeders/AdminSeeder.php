<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['admin_name' => 'admin'],
            [
                'fname' => 'Super',
                'lname' => 'Admin',
                'address' => '123 Main Street, City',
                'phone' => '09171234567',
                'email' => 'admin@washhour.com',
                'password' => 'admin123'
            ]
        );
    }
}
