<?php

namespace App\Services;

use App\Models\Admin;
use App\Services\MessageService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminService
{
    public function __construct(
        private MessageService $messageService,
        private GeocodeService $geocodeService
    ) {}

    //create admin
    public function createAdmin(array $data): array
    {
        try {
            // Wrap admin creation in database transaction to prevent race conditions
            $result = DB::transaction(function () use ($data) {
                // Re-validate uniqueness constraints inside transaction
                // This ensures atomicity and prevents concurrent registrations
                if (Admin::where('email', $data['email'])->exists()) {
                    throw new \Exception('Email is already registered');
                }

                if (Admin::where('phone', $data['phone'])->exists()) {
                    throw new \Exception('Phone number is already registered');
                }

                // Geocode address if provided
                if (!empty($data['address'])) {
                    $coords = $this->geocodeService->geocodeAddressFresh($data['address']);
                    if ($coords) {
                        $data['latitude'] = $coords['latitude'];
                        $data['longitude'] = $coords['longitude'];
                        $data['location_updated_at'] = now();
                    }
                }

                $admin = Admin::create([
                    'admin_name' => $data['admin_name'],
                    'fname' => $data['fname'],
                    'lname' => $data['lname'],
                    'address' => $data['address'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'location_updated_at' => $data['location_updated_at'] ?? null,
                ]);

                return $admin;
            });

            //send welcome message (outside transaction to avoid blocking)
            try {
                Log::info("Sending welcome message to admin: {$result->phone}");
                $this->messageService->sendWelcomeMessage($result->phone, [
                    'contact_number' => 'Place_Holder'
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send welcome message to admin: {$result->phone}. Error: " . $e->getMessage());
            }

            return [
                'admin' => $result,
                'message' => 'Admin created successfully.'
            ];
        } catch (QueryException $e) {
            // Handle database constraint violations
            if ($e->getCode() === '23000') {
                // Integrity constraint violation
                $errorMessage = $e->getMessage();

                if (str_contains($errorMessage, 'admins_email_unique')) {
                    throw new \Exception('Email is already registered');
                } elseif (str_contains($errorMessage, 'admins_phone_unique')) {
                    throw new \Exception('Phone number is already registered');
                }

                throw new \Exception('Admin creation failed due to duplicate data');
            }

            // Re-throw other database exceptions
            throw $e;
        }
    }

    //update admin
    public function updateAdmin(int $adminId, array $data): Admin
    {
        $admin = Admin::find($adminId);

        if (!$admin) {
            throw new \Exception("Admin not found.");
        }

        // If address changed, geocode it
        if (isset($data['address']) && $data['address'] !== $admin->address) {
            $coords = $this->geocodeService->geocodeAddressFresh($data['address']);
            if ($coords) {
                $data['latitude'] = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
                $data['location_updated_at'] = now();
            } else {
                $data['latitude'] = null;
                $data['longitude'] = null;
                $data['location_updated_at'] = null;
            }
        }

        $admin->update($data);
        return $admin->fresh();
    }

    //change password
    public function changePass(int $adminId, string $currentPassword, string $newPassword): bool
    {
        $admin = Admin::find($adminId);

        if (!$admin) {
            throw new \Exception("Admin not found with ID: {$adminId}");
        }

        //verify
        if (!Hash::check($currentPassword, $admin->password)) {
            throw new \Exception("Current password is incorrect.");
        }

        if (Hash::check($newPassword, $admin->password)) {
            throw new \Exception("New password cannot be the same as the current password.");
        }

        //update
        $admin->update([
            'password' => $newPassword
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
