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
        $validated = $request->validate([
            'status' => 'required|in:hadir,skip,disposisi',
            'to_leader_id' => 'required_if:status,disposisi|nullable|exists:leaders,id',
            'notes' => 'nullable|string',
        ]);

        // Assuming the current owner of the activity is the one making the disposition.
        // We'll use $activity->leader_id as the from_leader_id for now.
        $fromLeaderId = $activity->leader_id;

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
