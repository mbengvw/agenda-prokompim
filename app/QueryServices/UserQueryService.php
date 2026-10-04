<?php

namespace App\QueryServices;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserQueryService
{
    public function getAllUsers(): Collection
    {
        return User::with('roles')->get();
    }
}
