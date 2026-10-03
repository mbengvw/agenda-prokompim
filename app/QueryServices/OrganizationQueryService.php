<?php

namespace App\QueryServices;

use App\Models\Organization;
use Illuminate\Pagination\LengthAwarePaginator;

class OrganizationQueryService
{
    public function __construct(protected Organization $model)
    {
        //
    }

    public function getPaginatedOrganizations(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->model->latest();

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(type) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(contact_person) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
