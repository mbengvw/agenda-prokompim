<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\Organization;
use App\QueryServices\OrganizationQueryService;
use App\Services\OrganizationService;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function __construct(
        protected OrganizationQueryService $queryService,
        protected OrganizationService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $organizations = $this->queryService->getPaginatedOrganizations(10, $search);

        $orgTypes = [
            'OPD' => 'Organisasi Perangkat Daerah',
            'GOVERNMENT' => 'Instansi Pemerintah',
            'BUMD' => 'Badan Usaha Milik Daerah',
            'PRIVATE' => 'Perusahaan / Swasta',
            'COMMUNITY' => 'Kelompok Masyarakat',
            'ORGANIZATION' => 'Organisasi / Lembaga',
            'EDUCATIONAL' => 'Lembaga Pendidikan',
            'OTHER' => 'Lainnya',
        ];

        return view('organizations.index', compact('organizations', 'search', 'orgTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationRequest $request)
    {
        $organization = $this->service->createOrganization($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data organisasi berhasil ditambahkan.',
                'data' => $organization,
            ]);
        }

        return redirect()->route('organizations.index')->with('success', 'Data organisasi berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        $this->service->updateOrganization($organization, $request->validated());

        return redirect()->route('organizations.index')->with('success', 'Data organisasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        $this->service->deleteOrganization($organization);

        return redirect()->route('organizations.index')->with('success', 'Data organisasi berhasil dihapus.');
    }
}
