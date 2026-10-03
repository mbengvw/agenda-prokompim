<?php

namespace App\Repositories;

use App\Models\ActivityDisposition;

class ActivityDispositionRepository
{
    public function __construct(protected ActivityDisposition $model)
    {
        //
    }

    public function create(array $data): ActivityDisposition
    {
        return $this->model->create($data);
    }

    public function updateOrCreate(array $attributes, array $values = []): ActivityDisposition
    {
        return $this->model->updateOrCreate($attributes, $values);
    }
}
