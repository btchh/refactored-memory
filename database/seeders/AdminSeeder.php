<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'admin_name' => 'admin',
            'fname' => 'Admin',
            'lname' => 'User',
            'email' => 'admin@example.com',
            'phone' => '+1234567890',
            'address' => '123 Admin Street',
            'password' => 'password123'
        ]);

        Admin::create([
            'admin_name' => 'superadmin',
            'fname' => 'Super',
            'lname' => 'Admin',
            'email' => 'superadmin@example.com',
            'phone' => '+0987654321',
            'address' => '456 Super Street',
            'password' => 'superpass123'
        ]);
    }
}
