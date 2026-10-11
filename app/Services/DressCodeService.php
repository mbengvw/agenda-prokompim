<?php

namespace App\Services;

use App\Models\DressCode;
use App\Repositories\DressCodeRepository;

class DressCodeService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected DressCodeRepository $repository)
    {
        //
    }

    public function createDressCode(array $data): DressCode
    {
        return $this->repository->create($data);
    }

    public function updateDressCode(DressCode $dressCode, array $data): bool
    {
        return $this->repository->update($dressCode, $data);
    }

    public function deleteDressCode(DressCode $dressCode): bool
    {
        return $this->repository->delete($dressCode);
    }
}
