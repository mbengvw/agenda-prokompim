<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class PermissionService
{
    /**
     * Create a new permission.
     *
     * @param array<string, mixed> $data
     * @return Permission
     * @throws Exception
     */
    public function create(array $data): Permission
    {
        try {
            DB::beginTransaction();

            $permission = Permission::create([
                'name' => $data['name'],
                'guard_name' => 'web'
            ]);

            DB::commit();

            return $permission;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create permission: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update an existing permission.
     *
     * @param Permission $permission
     * @param array<string, mixed> $data
     * @return Permission
     * @throws Exception
     */
    public function update(Permission $permission, array $data): Permission
    {
        try {
            DB::beginTransaction();

            $permission->update([
                'name' => $data['name']
            ]);

            DB::commit();

            return $permission;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update permission: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a permission.
     *
     * @param Permission $permission
     * @return bool
     * @throws Exception
     */
    public function delete(Permission $permission): bool
    {
        try {
            DB::beginTransaction();
            
            $result = $permission->delete();

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete permission: ' . $e->getMessage());
            throw $e;
        }
    }
}
