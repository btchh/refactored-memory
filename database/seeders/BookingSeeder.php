<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        $statuses = ['Pending', 'In Progress', 'Completed', 'Delivered', 'Cancelled'];
        $services = [
            [
                ['name' => 'Wash & Fold', 'price' => 150],
                ['name' => 'Dry Cleaning', 'price' => 200]
            ],
            [
                ['name' => 'Iron Only', 'price' => 100]
            ],
            [
                ['name' => 'Wash & Fold', 'price' => 150],
                ['name' => 'Iron Only', 'price' => 100],
                ['name' => 'Dry Cleaning', 'price' => 200]
            ],
            [
                ['name' => 'Express Wash', 'price' => 250]
            ]
        ];

        foreach ($users as $user) {
            // Create 3-5 bookings per user
            $bookingCount = rand(3, 5);
            
            for ($i = 0; $i < $bookingCount; $i++) {
                $selectedServices = $services[array_rand($services)];
                $total = array_sum(array_column($selectedServices, 'price'));
                
                \App\Models\Booking::create([
                    'user_id' => $user->id,
                    'date' => now()->addDays(rand(-30, 30)),
                    'time' => sprintf('%02d:00', rand(8, 17)),
                    'status' => $statuses[array_rand($statuses)],
                    'services' => $selectedServices,
                    'total' => $total,
                    'notes' => rand(0, 1) ? 'Please handle with care' : null
                ]);
            }
        }

        $this->command->info('Bookings seeded successfully!');
    }
}
