<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\QueryServices\LocationQueryService;
use App\Services\LocationService;

class LocationController extends Controller
{
    public function __construct(
        protected LocationQueryService $queryService,
        protected LocationService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $locations = $this->queryService->getPaginatedLocations(10, $search);
        return view('locations.index', compact('locations', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request)
    {
        $this->service->createLocation($request->validated());

        return redirect()->route('locations.index')->with('success', 'Data lokasi berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location)
    {
        $this->service->updateLocation($location, $request->validated());

        return redirect()->route('locations.index')->with('success', 'Data lokasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $this->service->deleteLocation($location);

        return redirect()->route('locations.index')->with('success', 'Data lokasi berhasil dihapus.');
    }
}
