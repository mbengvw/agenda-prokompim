<?php

namespace App\Repositories;

use App\Models\Location;

class LocationRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Location $model)
    {
        //
    }

    public function create(array $data): Location
    {
        return $this->model->create($data);
    }

    public function update(Location $location, array $data): bool
    {
        return $location->update($data);
    }

    public function delete(Location $location): bool
    {
        return $location->delete();
    }
}
