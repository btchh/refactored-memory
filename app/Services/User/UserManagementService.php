<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\GeocodeService;
use App\Services\MessageService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserManagementService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private GeocodeService $geocodeService,
        private MessageService $messageService
    ) {}

    /**
     * Create a new user
     *
     * @param array $data User data including username, fname, lname, address, phone, email, password
     * @return array Array containing the created user and success message
     * @throws \Exception If validation fails or user already exists
     */
    public function createUser(array $data): array
    {
        try {
            // Wrap user creation in database transaction to prevent race conditions
            $result = DB::transaction(function () use ($data) {
                // Re-validate uniqueness constraints inside transaction
                // This ensures atomicity and prevents concurrent registrations
                if ($this->userRepository->existsByEmail($data['email'])) {
                    throw new \Exception('Email is already registered');
                }

                if ($this->userRepository->existsByPhone($data['phone'])) {
                    throw new \Exception('Phone number is already registered');
                }

                if ($this->userRepository->existsByUsername($data['username'])) {
                    throw new \Exception('Username is already taken');
                }

                // Geocode address if provided
                if (!empty($data['address'])) {
                    $coords = $this->geocodeService->geocodeAddressFresh($data['address']);
                    if ($coords) {
                        $data['latitude'] = $coords['latitude'];
                        $data['longitude'] = $coords['longitude'];
                    }
                }

                $user = $this->userRepository->create([
                    'username' => $data['username'],
                    'fname' => $data['fname'],
                    'lname' => $data['lname'],
                    'address' => $data['address'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                ]);

                return $user;
            });

            // Send welcome message (outside transaction to avoid blocking)
            try {
                Log::info("User created with phone: {$result->phone}");
                $this->messageService->sendWelcomeMessage($result->phone, [
                    'contact_number' => 'Place_Holder'
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send welcome message to user: {$result->phone}. Error: " . $e->getMessage());
            }

            return [
                'user' => $result,
                'message' => 'User registered successfully.'
            ];
        } catch (QueryException $e) {
            // Handle database constraint violations
            if ($e->getCode() === '23000') {
                // Integrity constraint violation
                $errorMessage = $e->getMessage();
                
                if (str_contains($errorMessage, 'users_email_unique')) {
                    throw new \Exception('Email is already registered');
                } elseif (str_contains($errorMessage, 'users_phone_unique')) {
                    throw new \Exception('Phone number is already registered');
                } elseif (str_contains($errorMessage, 'users_username_unique')) {
                    throw new \Exception('Username is already taken');
                }
                
                throw new \Exception('Registration failed due to duplicate data');
            }
            
            // Re-throw other database exceptions
            throw $e;
        }
    }

    /**
     * Update an existing user
     *
     * @param int $userId User ID to update
     * @param array $data Updated user data
     * @return User Updated user model
     * @throws \Exception If user not found
     */
    public function updateUser(int $userId, array $data): User
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new \Exception("User not found.");
        }

        // If address changed, geocode it
        if (isset($data['address']) && $data['address'] !== $user->address) {
            $coords = $this->geocodeService->geocodeAddressFresh($data['address']);
            if ($coords) {
                $data['latitude'] = $coords['latitude'];
                $data['longitude'] = $coords['longitude'];
            } else {
                $data['latitude'] = null;
                $data['longitude'] = null;
            }
        }

        return $this->userRepository->update($userId, $data);
    }

    /**
     * Delete a user
     *
     * @param int $userId User ID to delete
     * @return bool True if deletion was successful
     * @throws \Exception If user not found
     */
    public function deleteUser(int $userId): bool
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new \Exception("User not found.");
        }

        return $this->userRepository->delete($userId);
    }
}
