<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'username' => 'testuser1',
                'email' => 'john.doe@example.com',
            ],
            [
                'fname' => 'John',
                'lname' => 'Doe',
                'address' => '123 Main St, City',
                'phone' => '1234567890',
                'password' => 'password123',
            ]
        );

        User::updateOrCreate(
            [
                'username' => 'testuser2',
                'email' => 'jane.smith@example.com',
            ],
            [
                'fname' => 'Jane',
                'lname' => 'Smith',
                'address' => '456 Oak Ave, Town',
                'phone' => '0987654321',
                'password' => 'password123',
            ]
        );

        User::updateOrCreate(
            [
                'username' => 'testuser3',
                'email' => 'bob.johnson@example.com',
            ],
            [
                'fname' => 'Bob',
                'lname' => 'Johnson',
                'address' => '789 Pine Rd, Village',
                'phone' => '5551234567',
                'password' => 'password123',
            ]
        );
    }
}
