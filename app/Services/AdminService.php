<?php

namespace App\Services;

use App\Models\Admin;
use App\Services\MessageService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminService
{
    public function __construct(
        private MessageService $messageService
    ) {}

    //create admin
    public function createAdmin(array $data): array
    {
        $admin = Admin::create([
            'admin_name' => $data['admin_name'],
            'fname' => $data['fname'],
            'lname' => $data['lname'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        //send welcome message
        try {
            Log::info("Sending welcome message to admin: {$admin->phone}");
            $this->messageService->sendWelcomeMessage($admin->phone, [
                'contact_number' => 'Place_Holder'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send welcome message to admin: {$admin->phone}. Error: " . $e->getMessage());
        }

        return [
            'admin' => $admin,
            'message' => 'Admin created successfully.'
        ];
    }

    //update admin
    public function updateAdmin(Admin $adminId, array $data): Admin
    {
        $admin = Admin::find($adminId);

        if (!$admin) {
            throw new \Exception("Admin not found.");
        }

        $admin->update($data);
        return $admin->fresh();
    }

    //change password
    public function changePass(int $adminId, string $currentPassword, string $newPassword): bool
    {
        $admin = Admin::find($adminId);

        //verify
        if (!Hash::check($currentPassword, $admin->password)) {
            throw new \Exception("Current password is incorrect.");
        }

        if (Hash::check($newPassword, $admin->password)) {
            throw new \Exception("New password cannot be the same as the current password.");
        }

        //update
        $admin->update([
            'password' => Hash::make($newPassword)
        ]);

        return true;
    }

    //select all admin
    public function getAllAdmins()
    {
        return Admin::orderBy('id', 'desc')->get();
    }

    //select by id
    public function findAdmin(int $adminId): ?Admin
    {
        return Admin::find($adminId);
    }

    //select by name
    public function findAdminByName(string $adminName): ?Admin
    {
        return Admin::where('admin_name', $adminName)->first();
    }

    //select by email
    public function findAdminByEmail(string $email): ?Admin
    {
        return Admin::where('email', $email)->first();
    }

    //delete admin
    public function deleteAdmin(int $adminId): bool
    {
        $admin = Admin::findOrFail($adminId);
        return $admin->delete();
    }
}
