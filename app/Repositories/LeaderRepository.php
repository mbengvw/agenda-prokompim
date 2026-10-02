<?php

namespace App\Repositories;

use App\Models\Leader;

class LeaderRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Leader $model)
    {
        //
    }

    public function create(array $data): Leader
    {
        return $this->model->create($data);
    }

    public function update(Leader $leader, array $data): Leader
    {
        $leader->update($data);
        return $leader;
    }

    public function delete(Leader $leader): bool
    {
        return $leader->delete();
    }
}
