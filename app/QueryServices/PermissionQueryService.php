<?php

namespace App\QueryServices;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionQueryService
{
    /**
     * Get all permissions.
     *
     * @return Collection<int, Permission>
     */
    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }
}
