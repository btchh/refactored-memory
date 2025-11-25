<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Support\Collection;

class AdminRepository implements AdminRepositoryInterface
{
    /**
     * Find an admin by ID
     *
     * @param int $id
     * @return Admin|null
     */
    public function find(int $id): ?Admin
    {
        return Admin::find($id);
    }

    /**
     * Find an admin by email
     *
     * @param string $email
     * @return Admin|null
     */
    public function findByEmail(string $email): ?Admin
    {
        return Admin::where('email', $email)->first();
    }

    /**
     * Find an admin by phone
     *
     * @param string $phone
     * @return Admin|null
     */
    public function findByPhone(string $phone): ?Admin
    {
        return Admin::where('phone', $phone)->first();
    }

    /**
     * Find an admin by admin_name
     *
     * @param string $adminName
     * @return Admin|null
     */
    public function findByAdminName(string $adminName): ?Admin
    {
        return Admin::where('admin_name', $adminName)->first();
    }

    /**
     * Create a new admin
     *
     * @param array $data
     * @return Admin
     */
    public function create(array $data): Admin
    {
        return Admin::create($data);
    }

    /**
     * Update an admin
     *
     * @param int $id
     * @param array $data
     * @return Admin
     */
    public function update(int $id, array $data): Admin
    {
        $admin = Admin::findOrFail($id);
        $admin->update($data);
        return $admin->fresh();
    }

    /**
     * Delete an admin
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $admin = Admin::findOrFail($id);
        return $admin->delete();
    }

    /**
     * Get all admins
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Admin::all();
    }

    /**
     * Check if an admin exists by email
     *
     * @param string $email
     * @return bool
     */
    public function existsByEmail(string $email): bool
    {
        return Admin::where('email', $email)->exists();
    }

    /**
     * Check if an admin exists by phone
     *
     * @param string $phone
     * @return bool
     */
    public function existsByPhone(string $phone): bool
    {
        return Admin::where('phone', $phone)->exists();
    }
}
