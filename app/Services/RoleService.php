<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use App\Repositories\RoleRepository;

class RoleService
{
    public function __construct(protected RoleRepository $roleRepository)
    {
    }

    public function create(array $data): Role
    {
        $role = $this->roleRepository->create(['name' => $data['name']]);
        
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }
        
        return $role;
    }

    public function update(Role $role, array $data): bool
    {
        $result = $this->roleRepository->update($role, ['name' => $data['name']]);
        
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }
        
        return $result;
    }

    public function delete(Role $role): bool
    {
        return $this->roleRepository->delete($role);
    }
}
