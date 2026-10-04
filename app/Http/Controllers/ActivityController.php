<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Leader;
use App\Models\Location;
use App\Models\Organization;
use App\Models\ProtocolOfficer;
use App\QueryServices\ActivityQueryService;
use App\Services\ActivityDispositionService;
use App\Services\ActivityService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct(
        protected ActivityQueryService $queryService,
        protected ActivityService $service
    ) {}

    public function exportPdf(Request $request)
    {
        $dateFilter = $request->input('date', Carbon::today()->toDateString());
        
        $activities = Activity::with(['leader', 'location', 'organization', 'protocolOfficer', 'companions'])
            ->whereDate('activity_date', $dateFilter)
            ->where('status', 'approved')
            ->orderBy('start_time')
            ->get();

        $dateFormatted = Carbon::parse($dateFilter)->locale('id')->translatedFormat('l, j F Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('activities.pdf', compact('activities', 'dateFormatted'));
        
        return $pdf->download("Agenda_Kegiatan_{$dateFilter}.pdf");
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $leaderFilter = $request->input('leader');
        $dateFilter = $request->input('date', Carbon::today()->toDateString());
        $statusFilter = $request->input('status');

        $activities = $this->queryService->getPaginatedActivities(10, $search, $leaderFilter, $dateFilter, $statusFilter);
        $timelineData = $this->queryService->getTimelineData($dateFilter, null); // Status removed

        $leaders = Leader::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $protocolOfficers = ProtocolOfficer::where('is_active', true)->orderBy('name')->get();

        return view('activities.index', compact('activities', 'timelineData', 'search', 'leaderFilter', 'dateFilter', 'statusFilter', 'leaders', 'locations', 'organizations', 'protocolOfficers'));
    }

    public function create(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        $leaders = Leader::where('is_active', true)->orderBy('name')->get()->map(function ($leader) {
            $pos = strtolower($leader->position);
            if (str_contains($pos, 'wakil bupati')) {
                $leader->level = 2;
            } elseif (str_contains($pos, 'bupati')) {
                $leader->level = 1;
            } elseif (str_contains($pos, 'sekda') || str_contains($pos, 'sekretaris daerah')) {
                $leader->level = 3;
            } else {
                $leader->level = 4;
            }

            return $leader;
        });

        $user = auth()->user();
        $mainLeaders = $leaders->filter(function ($leader) use ($user) {
            if ($user->hasRole('ajudan_bupati')) {
                return $leader->level === 1;
            }
            if ($user->hasRole('ajudan_wabup')) {
                return $leader->level === 2;
            }
            if ($user->hasRole('ajudan_sekda')) {
                return $leader->level === 3;
            }

            return in_array($leader->level, [1, 2, 3]);
        });

        $locations = Location::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $protocolOfficers = ProtocolOfficer::where('is_active', true)->orderBy('name')->get();

        return view('activities.create', compact('leaders', 'mainLeaders', 'locations', 'organizations', 'protocolOfficers', 'date'));
    }

    public function store(StoreActivityRequest $request)
    {
        $data = $request->validated();
        $data['is_disposition'] = $request->boolean('is_disposition');

        if (! empty($data['location_input'])) {
            $location = Location::where('name', $data['location_input'])->first();
            if ($location) {
                $data['location_id'] = $location->id;
                $data['location_text'] = null;
            } else {
                $data['location_id'] = null;
                $data['location_text'] = $data['location_input'];
            }
        }

        if (! empty($data['organizer_input'])) {
            $organizer = Organization::where('name', $data['organizer_input'])->first();
            if ($organizer) {
                $data['organization_id'] = $organizer->id;
                $data['organizer_text'] = null;
            } else {
                $data['organization_id'] = null;
                $data['organizer_text'] = $data['organizer_input'];
            }
        }

        unset($data['location_input'], $data['organizer_input']);

        $user = auth()->user();
        $data['created_by'] = $user?->id;

        if ($user && str_contains($user->roles->first()?->name ?? '', 'ajudan_')) {
            $data['status'] = 'submitted';
        } else {
            $data['status'] = 'draft';
        }

        $activity = $this->service->createActivity($data);

        if ($user && str_contains($user->roles->first()?->name ?? '', 'ajudan_')) {
            if ($activity->leader_id) {
                app(ActivityDispositionService::class)->handleDisposition(
                    $activity,
                    'hadir',
                    $activity->leader_id,
                    null,
                    'Dibuat langsung oleh Ajudan.'
                );
            }
        }

        if ($data['status'] === 'approved') {
            $msg = 'Agenda berhasil ditambahkan.';
        } elseif ($data['status'] === 'submitted') {
            $msg = 'Agenda berhasil ditambahkan dan menunggu verifikasi Prokompim.';
        } else {
            $msg = 'Agenda berhasil ditambahkan (Draf).';
        }

        return redirect()->route('activities.index')->with('success', $msg);
    }

    public function show(Activity $activity)
    {
        $activity->load(['leader', 'companions', 'location', 'organization', 'protocolOfficer']);

        $activityLogs = \Spatie\Activitylog\Models\Activity::where('subject_type', Activity::class)
            ->where('subject_id', $activity->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('activities.show', compact('activity', 'activityLogs'));
    }

    public function edit(Activity $activity)
    {
        $leaders = Leader::where('is_active', true)->orderBy('name')->get()->map(function ($leader) {
            $pos = strtolower($leader->position);
            if (str_contains($pos, 'wakil bupati')) {
                $leader->level = 2;
            } elseif (str_contains($pos, 'bupati')) {
                $leader->level = 1;
            } elseif (str_contains($pos, 'sekda') || str_contains($pos, 'sekretaris daerah')) {
                $leader->level = 3;
            } else {
                $leader->level = 4;
            }

            return $leader;
        });

        $user = auth()->user();
        $mainLeaders = $leaders->filter(function ($leader) use ($user) {
            if ($user->hasRole('ajudan_bupati')) {
                return $leader->level === 1;
            }
            if ($user->hasRole('ajudan_wabup')) {
                return $leader->level === 2;
            }
            if ($user->hasRole('ajudan_sekda')) {
                return $leader->level === 3;
            }

            return in_array($leader->level, [1, 2, 3]);
        });

        $locations = Location::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $protocolOfficers = ProtocolOfficer::where('is_active', true)->orderBy('name')->get();

        return view('activities.edit', compact('activity', 'leaders', 'mainLeaders', 'locations', 'organizations', 'protocolOfficers'));
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
                $location = Location::where('name', $data['location_input'])->first();
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
                $organizer = Organization::where('name', $data['organizer_input'])->first();
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
            'status' => 'required|in:draft,submitted,revision,approved,rejected,cancelled',
            'revision_notes' => 'nullable|string',
        ]);

        $this->service->updateStatus($activity, $validated['status'], $validated['revision_notes'] ?? null);

        $message = 'Status agenda diubah menjadi '.ucfirst($validated['status']);
        if ($validated['status'] === 'revision') {
            $message .= ' dengan catatan: '.$validated['revision_notes'];
        }

        activity()
            ->performedOn($activity)
            ->causedBy(auth()->user())
            ->log($message);

        return redirect()->back()->with('success', 'Status agenda berhasil diperbarui.');
    }

    public function destroy(Activity $activity)
    {
        $user = auth()->user();
        $isCreator = $activity->created_by === $user->id;
        $isAjudan = str_contains($user->roles->first()?->name ?? '', 'ajudan_');

        if (!$isCreator && !$isAjudan) {
            abort(403, 'Hanya pembuat agenda dan ajudan yang diizinkan untuk menghapus.');
        }

        $this->service->deleteActivity($activity);

        return redirect()->route('activities.index')->with('success', 'Agenda berhasil dihapus.');
    }

    public function disposition(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'disposition_to_id' => 'required|exists:leaders,id',
        ]);

        $activity->update([
            'is_disposition' => true,
            'disposition_to_id' => $validated['disposition_to_id'],
        ]);

        $leader = Leader::find($validated['disposition_to_id']);

        activity()
            ->performedOn($activity)
            ->causedBy(auth()->user())
            ->log('Mendisposisikan agenda ini kepada: '.$leader->name.' ('.$leader->position.')');

        return redirect()->back()->with('success', 'Agenda berhasil didisposisikan.');
    }
}
