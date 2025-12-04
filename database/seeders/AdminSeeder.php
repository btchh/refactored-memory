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
        // Branch 1 - Main Branch (Lipa City)
        $mainBranchName = 'WashHour Main';
        $mainBranchAddress = 'B6 L15 City Park Ave., Sabang, Lipa City';
        
        Admin::create([
            'username' => 'juan.main',
            'fname' => 'Juan',
            'lname' => 'Dela Cruz',
            'email' => 'juan.main@washhour.com',
            'phone' => '09217769999',
            'address' => $mainBranchAddress,
            'branch_name' => $mainBranchName,
            'branch_address' => $mainBranchAddress,
            'password' => 'password123'
        ]);

        Admin::create([
            'username' => 'pedro.main',
            'fname' => 'Pedro',
            'lname' => 'Reyes',
            'email' => 'pedro.main@washhour.com',
            'phone' => '09217769998',
            'address' => $mainBranchAddress,
            'branch_name' => $mainBranchName,
            'branch_address' => $mainBranchAddress,
            'password' => 'password123'
        ]);

        Admin::create([
            'username' => 'ana.main',
            'fname' => 'Ana',
            'lname' => 'Garcia',
            'email' => 'ana.main@washhour.com',
            'phone' => '09217769997',
            'address' => $mainBranchAddress,
            'branch_name' => $mainBranchName,
            'branch_address' => $mainBranchAddress,
            'password' => 'password123'
        ]);

        // Branch 2 - Rosario Branch
        $rosarioBranchName = 'WashHour Rosario';
        $rosarioBranchAddress = 'Bagong Pook, Rosario, Batangas';
        
        Admin::create([
            'username' => 'maria.rosario',
            'fname' => 'Maria',
            'lname' => 'Santos',
            'email' => 'maria.rosario@washhour.com',
            'phone' => '09181234567',
            'address' => $rosarioBranchAddress,
            'branch_name' => $rosarioBranchName,
            'branch_address' => $rosarioBranchAddress,
            'password' => 'password123'
        ]);

        Admin::create([
            'username' => 'jose.rosario',
            'fname' => 'Jose',
            'lname' => 'Ramos',
            'email' => 'jose.rosario@washhour.com',
            'phone' => '09181234568',
            'address' => $rosarioBranchAddress,
            'branch_name' => $rosarioBranchName,
            'branch_address' => $rosarioBranchAddress,
            'password' => 'password123'
        ]);

        // Branch 3 - Batangas City Branch
        $batangasBranchName = 'WashHour Batangas';
        $batangasBranchAddress = 'P. Burgos Street, Batangas City';
        
        Admin::create([
            'username' => 'carlos.batangas',
            'fname' => 'Carlos',
            'lname' => 'Mendoza',
            'email' => 'carlos.batangas@washhour.com',
            'phone' => '09171112222',
            'address' => $batangasBranchAddress,
            'branch_name' => $batangasBranchName,
            'branch_address' => $batangasBranchAddress,
            'password' => 'password123'
        ]);

        Admin::create([
            'username' => 'rosa.batangas',
            'fname' => 'Rosa',
            'lname' => 'Cruz',
            'email' => 'rosa.batangas@washhour.com',
            'phone' => '09171112223',
            'address' => $batangasBranchAddress,
            'branch_name' => $batangasBranchName,
            'branch_address' => $batangasBranchAddress,
            'password' => 'password123'
        ]);

        Admin::create([
            'username' => 'miguel.batangas',
            'fname' => 'Miguel',
            'lname' => 'Torres',
            'email' => 'miguel.batangas@washhour.com',
            'phone' => '09171112224',
            'address' => $batangasBranchAddress,
            'branch_name' => $batangasBranchName,
            'branch_address' => $batangasBranchAddress,
            'password' => 'password123'
        ]);

        // Branch 4 - Tanauan Branch
        $tanauanBranchName = 'WashHour Tanauan';
        $tanauanBranchAddress = 'J.P. Laurel Highway, Tanauan City, Batangas';
        
        Admin::create([
            'username' => 'elena.tanauan',
            'fname' => 'Elena',
            'lname' => 'Villanueva',
            'email' => 'elena.tanauan@washhour.com',
            'phone' => '09161234567',
            'address' => $tanauanBranchAddress,
            'branch_name' => $tanauanBranchName,
            'branch_address' => $tanauanBranchAddress,
            'password' => 'password123'
        ]);

        Admin::create([
            'username' => 'roberto.tanauan',
            'fname' => 'Roberto',
            'lname' => 'Fernandez',
            'email' => 'roberto.tanauan@washhour.com',
            'phone' => '09161234568',
            'address' => $tanauanBranchAddress,
            'branch_name' => $tanauanBranchName,
            'branch_address' => $tanauanBranchAddress,
            'password' => 'password123'
        ]);
    }
}
