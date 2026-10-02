<?php

namespace App\Services;

use App\Models\Organization;
use App\Repositories\OrganizationRepository;

class OrganizationService
{
    public function __construct(protected OrganizationRepository $repository)
    {
        //
    }

    public function createOrganization(array $data): Organization
    {
        return $this->repository->create($data);
    }

    public function updateOrganization(Organization $organization, array $data): bool
    {
        return $this->repository->update($organization, $data);
    }

    public function deleteOrganization(Organization $organization): bool
    {
        return $this->repository->delete($organization);
    }
}
