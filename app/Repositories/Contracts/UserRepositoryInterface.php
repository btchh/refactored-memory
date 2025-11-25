<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    /**
     * Find a user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function find(int $id): ?User;

    /**
     * Find a user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find a user by phone
     *
     * @param string $phone
     * @return User|null
     */
    public function findByPhone(string $phone): ?User;

    /**
     * Find a user by username
     *
     * @param string $username
     * @return User|null
     */
    public function findByUsername(string $username): ?User;

    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Update a user
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function update(int $id, array $data): User;

    /**
     * Delete a user
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Check if a user exists by email
     *
     * @param string $email
     * @return bool
     */
    public function existsByEmail(string $email): bool;

    /**
     * Check if a user exists by phone
     *
     * @param string $phone
     * @return bool
     */
    public function existsByPhone(string $phone): bool;

    /**
     * Check if a user exists by username
     *
     * @param string $username
     * @return bool
     */
    public function existsByUsername(string $username): bool;
}
