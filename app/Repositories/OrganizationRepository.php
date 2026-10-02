<?php

namespace App\Repositories;

use App\Models\Organization;

class OrganizationRepository
{
    public function __construct(protected Organization $model)
    {
        //
    }

    public function create(array $data): Organization
    {
        return $this->model->create($data);
    }

    public function update(Organization $organization, array $data): bool
    {
        return $organization->update($data);
    }

    public function delete(Organization $organization): bool
    {
        return $organization->delete();
    }
}
