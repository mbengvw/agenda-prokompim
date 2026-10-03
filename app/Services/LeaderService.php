<?php

namespace App\Services;

use App\Models\Leader;
use App\Repositories\LeaderRepository;

class LeaderService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected LeaderRepository $repository)
    {
        //
    }

    public function createLeader(array $data): Leader
    {
        $data['is_active'] = $data['is_active'] ?? true;

        return $this->repository->create($data);
    }

    public function updateLeader(Leader $leader, array $data): Leader
    {
        $data['is_active'] = $data['is_active'] ?? true;

        return $this->repository->update($leader, $data);
    }

    public function deleteLeader(Leader $leader): bool
    {
        return $this->repository->delete($leader);
    }
}
