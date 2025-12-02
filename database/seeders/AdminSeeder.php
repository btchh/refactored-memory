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
        // Branch 1 - Main Branch
        Admin::create([
            'admin_name' => 'WashHour Main',
            'fname' => 'Juan',
            'lname' => 'Dela Cruz',
            'email' => 'main@washhour.com',
            'phone' => '09217769999',
            'address' => 'B6 L15 City Park Ave., Sabang, Lipa',
            'branch_address' => 'B6 L15 City Park Ave., Sabang, Lipa City',
            'password' => 'password123'
        ]);

        // Branch 2 - Rosario Branch
        Admin::create([
            'admin_name' => 'WashHour Rosario',
            'fname' => 'Maria',
            'lname' => 'Santos',
            'email' => 'rosario@washhour.com',
            'phone' => '09181234567',
            'address' => 'Bagong Pook, Rosario, Batangas',
            'branch_address' => 'Bagong Pook, Rosario, Batangas',
            'password' => 'password123'
        ]);
    }
}
