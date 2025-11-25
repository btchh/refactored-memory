<?php

namespace App\Repositories\Contracts;

use App\Models\Admin;
use Illuminate\Support\Collection;

interface AdminRepositoryInterface
{
    /**
     * Find an admin by ID
     *
     * @param int $id
     * @return Admin|null
     */
    public function find(int $id): ?Admin;

    /**
     * Find an admin by email
     *
     * @param string $email
     * @return Admin|null
     */
    public function findByEmail(string $email): ?Admin;

    /**
     * Find an admin by phone
     *
     * @param string $phone
     * @return Admin|null
     */
    public function findByPhone(string $phone): ?Admin;

    /**
     * Find an admin by admin_name
     *
     * @param string $adminName
     * @return Admin|null
     */
    public function findByAdminName(string $adminName): ?Admin;

    /**
     * Create a new admin
     *
     * @param array $data
     * @return Admin
     */
    public function create(array $data): Admin;

    /**
     * Update an admin
     *
     * @param int $id
     * @param array $data
     * @return Admin
     */
    public function update(int $id, array $data): Admin;

    /**
     * Delete an admin
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get all admins
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Check if an admin exists by email
     *
     * @param string $email
     * @return bool
     */
    public function existsByEmail(string $email): bool;

    /**
     * Check if an admin exists by phone
     *
     * @param string $phone
     * @return bool
     */
    public function existsByPhone(string $phone): bool;
}
