<?php

namespace App\QueryServices;

use App\Models\ProtocolOfficer;
use Illuminate\Pagination\LengthAwarePaginator;

class ProtocolOfficerQueryService
{
    public function __construct(protected ProtocolOfficer $model)
    {
        //
    }

    public function getPaginatedProtocolOfficers(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->model->latest();

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(position) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(employee_number) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
