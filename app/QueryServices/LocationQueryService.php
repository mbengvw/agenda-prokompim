<?php

namespace App\QueryServices;

use App\Models\Location;
use Illuminate\Pagination\LengthAwarePaginator;

class LocationQueryService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Location $model)
    {
        //
    }

    public function getPaginatedLocations(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->model->latest();

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(city) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(address) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
