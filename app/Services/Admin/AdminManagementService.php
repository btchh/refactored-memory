<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Services\GeocodeService;
use App\Services\MessageService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminManagementService
{
    public function __construct(
        private AdminRepositoryInterface $adminRepository,
        private GeocodeService $geocodeService,
        private MessageService $messageService
    ) {}

    /**
     * Create a new admin
     *
     * @param array $data Admin data including admin_name, fname, lname, address, phone, email, password
     * @return array Array containing the created admin and success message
     * @throws \Exception If email or phone already exists
     */
    public function createAdmin(array $data): array
    {
        try {
            // Wrap admin creation in database transaction to prevent race conditions
            $result = DB::transaction(function () use ($data) {
                // Re-validate uniqueness constraints inside transaction
                // This ensures atomicity and prevents concurrent registrations
                if ($this->adminRepository->existsByEmail($data['email'])) {
                    throw new \Exception('Email is already registered');
                }

                if ($this->adminRepository->existsByPhone($data['phone'])) {
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

                $admin = $this->adminRepository->create([
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

    /**
     * Update an existing admin
     *
     * @param int $adminId Admin ID to update
     * @param array $data Updated admin data
     * @return Admin Updated admin model
     * @throws \Exception If admin not found
     */
    public function updateAdmin(int $adminId, array $data): Admin
    {
        $admin = $this->adminRepository->find($adminId);

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

        return $this->adminRepository->update($adminId, $data);
    }

    /**
     * Delete an admin
     *
     * @param int $adminId Admin ID to delete
     * @return bool True if deletion was successful
     */
    public function deleteAdmin(int $adminId): bool
    {
        return $this->adminRepository->delete($adminId);
    }

    /**
     * Get all admins
     *
     * @return Collection Collection of all admins
     */
    public function getAllAdmins(): Collection
    {
        return $this->adminRepository->all();
    }
}
