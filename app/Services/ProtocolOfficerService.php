<?php

namespace App\Services;

use App\Models\ProtocolOfficer;
use App\Repositories\ProtocolOfficerRepository;

class ProtocolOfficerService
{
    public function __construct(protected ProtocolOfficerRepository $repository)
    {
        //
    }

    public function createProtocolOfficer(array $data): ProtocolOfficer
    {
        return $this->repository->create($data);
    }

    public function updateProtocolOfficer(ProtocolOfficer $protocolOfficer, array $data): bool
    {
        return $this->repository->update($protocolOfficer, $data);
    }

    public function deleteProtocolOfficer(ProtocolOfficer $protocolOfficer): bool
    {
        return $this->repository->delete($protocolOfficer);
    }
}
