<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaderRequest;
use App\Http\Requests\UpdateLeaderRequest;
use App\Models\Leader;
use App\QueryServices\LeaderQueryService;
use App\Services\LeaderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaderController extends Controller
{
    public function __construct(
        protected LeaderQueryService $queryService,
        protected LeaderService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request): View
    {
        $search = $request->input('search');
        $leaders = $this->queryService->getPaginatedLeaders(10, $search);
        
        return view('leaders.index', compact('leaders', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('leaders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeaderRequest $request): RedirectResponse
    {
        $this->service->createLeader($request->validated());
        return redirect()->route('leaders.index')->with('success', 'Leader created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Leader $leader)
    {
        // Not implemented for now
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Leader $leader): View
    {
        return view('leaders.edit', compact('leader'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaderRequest $request, Leader $leader): RedirectResponse
    {
        $this->service->updateLeader($leader, $request->validated());
        return redirect()->route('leaders.index')->with('success', 'Leader updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Leader $leader): RedirectResponse
    {
        $this->service->deleteLeader($leader);
        return redirect()->route('leaders.index')->with('success', 'Leader deleted successfully.');
    }
}
