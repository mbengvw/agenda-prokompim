<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function create(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user = $this->userRepository->create($data);
        
        if (isset($data['roles'])) {
            $user->assignRole($data['roles']);
        }
        
        return $user;
    }

    public function update(User $user, array $data): bool
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $result = $this->userRepository->update($user, $data);
        
        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }
        
        return $result;
    }

    public function delete(User $user): bool
    {
        return $this->userRepository->delete($user);
    }

    public function toggleStatus(User $user): bool
    {
        return $this->userRepository->update($user, ['is_active' => !$user->is_active]);
    }
}
