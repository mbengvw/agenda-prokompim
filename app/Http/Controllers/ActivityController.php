<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\QueryServices\ActivityQueryService;
use App\Services\ActivityService;

class ActivityController extends Controller
{
    public function __construct(
        protected ActivityQueryService $queryService,
        protected ActivityService $service
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');
        $dateFilter = $request->input('date', \Carbon\Carbon::today()->toDateString());
        
        $activities = $this->queryService->getPaginatedActivities(10, $search, $statusFilter, $dateFilter);
        $timelineData = $this->queryService->getTimelineData($dateFilter, $statusFilter);
        
        $leaders = \App\Models\Leader::where('is_active', true)->orderBy('name')->get();
        $locations = \App\Models\Location::orderBy('name')->get();
        $organizations = \App\Models\Organization::orderBy('name')->get();
        $protocolOfficers = \App\Models\ProtocolOfficer::where('is_active', true)->orderBy('name')->get();

        return view('activities.index', compact('activities', 'timelineData', 'search', 'statusFilter', 'dateFilter', 'leaders', 'locations', 'organizations', 'protocolOfficers'));
    }

    public function store(StoreActivityRequest $request)
    {
        $data = $request->validated();
        $data['is_disposition'] = $request->boolean('is_disposition');
        
        if (!empty($data['location_input'])) {
            $location = \App\Models\Location::where('name', $data['location_input'])->first();
            if ($location) {
                $data['location_id'] = $location->id;
                $data['location_text'] = null;
            } else {
                $data['location_id'] = null;
                $data['location_text'] = $data['location_input'];
            }
        }

        if (!empty($data['organizer_input'])) {
            $organizer = \App\Models\Organization::where('name', $data['organizer_input'])->first();
            if ($organizer) {
                $data['organization_id'] = $organizer->id;
                $data['organizer_text'] = null;
            } else {
                $data['organization_id'] = null;
                $data['organizer_text'] = $data['organizer_input'];
            }
        }

        unset($data['location_input'], $data['organizer_input']);

        $this->service->createActivity($data);
        return redirect()->route('activities.index')->with('success', 'Agenda berhasil ditambahkan (Draf).');
    }

    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $data = $request->validated();
        $data['is_disposition'] = $request->boolean('is_disposition');
        
        if (array_key_exists('location_input', $data)) {
            if (empty($data['location_input'])) {
                $data['location_id'] = null;
                $data['location_text'] = null;
            } else {
                $location = \App\Models\Location::where('name', $data['location_input'])->first();
                if ($location) {
                    $data['location_id'] = $location->id;
                    $data['location_text'] = null;
                } else {
                    $data['location_id'] = null;
                    $data['location_text'] = $data['location_input'];
                }
            }
        }

        if (array_key_exists('organizer_input', $data)) {
            if (empty($data['organizer_input'])) {
                $data['organization_id'] = null;
                $data['organizer_text'] = null;
            } else {
                $organizer = \App\Models\Organization::where('name', $data['organizer_input'])->first();
                if ($organizer) {
                    $data['organization_id'] = $organizer->id;
                    $data['organizer_text'] = null;
                } else {
                    $data['organization_id'] = null;
                    $data['organizer_text'] = $data['organizer_input'];
                }
            }
        }

        unset($data['location_input'], $data['organizer_input']);

        $this->service->updateActivity($activity, $data);
        return redirect()->route('activities.index')->with('success', 'Agenda berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,submitted,revision,approved,cancelled',
            'revision_notes' => 'nullable|string'
        ]);

        $this->service->updateStatus($activity, $validated['status'], $validated['revision_notes'] ?? null);
        return redirect()->back()->with('success', 'Status agenda berhasil diperbarui.');
    }

    public function destroy(Activity $activity)
    {
        $this->service->deleteActivity($activity);
        return redirect()->route('activities.index')->with('success', 'Agenda berhasil dihapus.');
    }
}
