<?php

namespace App\Repositories;

use App\Models\DressCode;

class DressCodeRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected DressCode $model)
    {
        //
    }

    public function create(array $data): DressCode
    {
        return $this->model->create($data);
    }

    public function update(DressCode $dressCode, array $data): bool
    {
        return $dressCode->update($data);
    }

    public function delete(DressCode $dressCode): bool
    {
        return $dressCode->delete();
    }
}
