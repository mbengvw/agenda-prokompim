<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDressCodeRequest;
use App\Http\Requests\UpdateDressCodeRequest;
use App\Models\DressCode;
use App\QueryServices\DressCodeQueryService;
use App\Services\DressCodeService;
use Illuminate\Http\Request;

class DressCodeController extends Controller
{
    public function __construct(
        protected DressCodeQueryService $queryService,
        protected DressCodeService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dressCodes = $this->queryService->getPaginatedDressCodes(10, $search);

        return view('dress-codes.index', compact('dressCodes', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDressCodeRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $dressCode = $this->service->createDressCode($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pakaian berhasil ditambahkan.',
                'data' => $dressCode,
            ]);
        }

        return redirect()->route('dress-codes.index')->with('success', 'Data pakaian berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDressCodeRequest $request, DressCode $dressCode)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->service->updateDressCode($dressCode, $data);

        return redirect()->route('dress-codes.index')->with('success', 'Data pakaian berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DressCode $dressCode)
    {
        $this->service->deleteDressCode($dressCode);

        return redirect()->route('dress-codes.index')->with('success', 'Data pakaian berhasil dihapus.');
    }
}
