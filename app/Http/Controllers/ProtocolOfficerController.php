<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProtocolOfficerRequest;
use App\Http\Requests\UpdateProtocolOfficerRequest;
use App\Models\ProtocolOfficer;
use App\QueryServices\ProtocolOfficerQueryService;
use App\Services\ProtocolOfficerService;
use Illuminate\Http\Request;

class ProtocolOfficerController extends Controller
{
    public function __construct(
        protected ProtocolOfficerQueryService $queryService,
        protected ProtocolOfficerService $service
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $protocolOfficers = $this->queryService->getPaginatedProtocolOfficers(10, $search);

        return view('protocol_officers.index', compact('protocolOfficers', 'search'));
    }

    public function store(StoreProtocolOfficerRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $this->service->createProtocolOfficer($data);

        return redirect()->route('protocol-officers.index')->with('success', 'Petugas Protokol berhasil ditambahkan.');
    }

    public function update(UpdateProtocolOfficerRequest $request, ProtocolOfficer $protocolOfficer)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $this->service->updateProtocolOfficer($protocolOfficer, $data);

        return redirect()->route('protocol-officers.index')->with('success', 'Petugas Protokol berhasil diperbarui.');
    }

    public function destroy(ProtocolOfficer $protocolOfficer)
    {
        $this->service->deleteProtocolOfficer($protocolOfficer);

        return redirect()->route('protocol-officers.index')->with('success', 'Petugas Protokol berhasil dihapus.');
    }
}
