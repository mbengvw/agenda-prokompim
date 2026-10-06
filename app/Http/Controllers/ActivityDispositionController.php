<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Services\ActivityDispositionService;
use Illuminate\Http\Request;

class ActivityDispositionController extends Controller
{
    public function __construct(protected ActivityDispositionService $dispositionService) {}

    public function store(Request $request, Activity $activity)
    {
        if (!str_contains(auth()->user()->roles->first()?->name ?? '', 'ajudan_')) {
            abort(403, 'Hanya ajudan yang dapat mengelola kehadiran dan disposisi.');
        }

        $validated = $request->validate([
            'status' => 'required|in:hadir,skip,disposisi',
            'to_leader_id' => 'required_if:status,disposisi|nullable|exists:leaders,id',
            'notes' => 'nullable|string',
            'from_leader_id' => 'required|exists:leaders,id',
        ]);

        $fromLeaderId = $validated['from_leader_id'];

        $this->dispositionService->handleDisposition(
            $activity,
            $validated['status'],
            $fromLeaderId,
            $validated['to_leader_id'] ?? null,
            $validated['notes'] ?? null
        );

        return back()->with('success', 'Status kehadiran/disposisi berhasil diperbarui.');
    }
}
