<?php

namespace App\Services;

use App\Models\Location;
use App\Repositories\LocationRepository;

class LocationService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected LocationRepository $repository)
    {
        //
    }

    public function createLocation(array $data): Location
    {
        return $this->repository->create($data);
    }

    public function updateLocation(Location $location, array $data): bool
    {
        return $this->repository->update($location, $data);
    }

    public function deleteLocation(Location $location): bool
    {
        return $this->repository->delete($location);
    }
}
