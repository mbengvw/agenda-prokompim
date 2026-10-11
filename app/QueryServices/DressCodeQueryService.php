<?php

namespace App\QueryServices;

use App\Models\DressCode;
use Illuminate\Pagination\LengthAwarePaginator;

class DressCodeQueryService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected DressCode $model)
    {
        //
    }

    public function getPaginatedDressCodes(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->model->latest();

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
