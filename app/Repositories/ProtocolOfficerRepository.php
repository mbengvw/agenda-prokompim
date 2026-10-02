<?php

namespace App\Repositories;

use App\Models\ProtocolOfficer;

class ProtocolOfficerRepository
{
    public function __construct(protected ProtocolOfficer $model)
    {
        //
    }

    public function create(array $data): ProtocolOfficer
    {
        return $this->model->create($data);
    }

    public function update(ProtocolOfficer $protocolOfficer, array $data): bool
    {
        return $protocolOfficer->update($data);
    }

    public function delete(ProtocolOfficer $protocolOfficer): bool
    {
        return $protocolOfficer->delete();
    }
}
