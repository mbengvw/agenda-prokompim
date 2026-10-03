<?php

namespace App\QueryServices;

use App\Models\Leader;
use Illuminate\Pagination\LengthAwarePaginator;

class LeaderQueryService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Leader $model)
    {
        //
    }

    public function getPaginatedLeaders(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->model->latest();

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(position) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
