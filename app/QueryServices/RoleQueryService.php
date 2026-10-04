<?php

namespace App\QueryServices;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleQueryService
{
    public function getAllRoles(): Collection
    {
        return Role::with('permissions')->get();
    }
}
