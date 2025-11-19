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
            'address' => 'Bagong Pook Rosario Batangas',
            'password' => 'password123'
        ]);
    }
}
