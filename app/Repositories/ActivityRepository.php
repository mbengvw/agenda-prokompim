<?php

namespace App\Repositories;

use App\Models\Activity;

class ActivityRepository
{
    public function __construct(protected Activity $model)
    {
        //
    }

    public function create(array $data): Activity
    {
        $data['created_by'] = auth()->id() ?? $data['created_by'] ?? null;

        return $this->model->create($data);
    }

    public function update(Activity $activity, array $data): bool
    {
        return $activity->update($data);
    }

    public function delete(Activity $activity): bool
    {
        return $activity->delete();
    }

    public function restore(Activity $activity): bool
    {
        return $activity->restore();
    }
}
