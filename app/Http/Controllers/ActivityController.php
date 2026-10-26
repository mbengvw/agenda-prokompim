<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\DressCode;
use App\Models\Leader;
use App\Models\Location;
use App\Models\Organization;
use App\Models\ProtocolOfficer;
use App\QueryServices\ActivityQueryService;
use App\Services\ActivityDispositionService;
use App\Services\ActivityService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    public function __construct(
        protected ActivityQueryService $queryService,
        protected ActivityService $service
    ) {}

    public function exportPdf(Request $request)
    {
        $dateFilter = $request->input('date', Carbon::today()->toDateString());
        $leaderId = $request->input('leader_id');

        if (! $leaderId) {
            return redirect()->back()->withErrors('Pimpinan harus dipilih untuk mencetak PDF.');
        }

        $leader = Leader::findOrFail($leaderId);

        // Dihadiri:
        // Kegiatan yang bola akhirnya ada di dia dan dikonfirmasi 'hadir'
        $dihadiri = Activity::with(['leader', 'location', 'organization', 'protocolOfficer', 'companions'])
            ->whereDate('activity_date', $dateFilter)
            ->whereIn('status', ['approved', 'submitted'])
            ->where('leader_id', $leaderId)
            ->whereHas('dispositions', function ($q) use ($leaderId) {
                $q->where('from_leader_id', $leaderId)
                    ->where('status', 'hadir');
            })
            ->orderBy('start_time')
            ->get();

        // Didisposisi:
        // Kegiatan yang pernah dia disposisikan ke orang lain, dan bola akhirnya BUKAN di dia
        $didisposisi = Activity::with(['leader', 'location', 'organization', 'protocolOfficer', 'companions'])
            ->whereDate('activity_date', $dateFilter)
            ->whereIn('status', ['approved', 'submitted'])
            ->where('leader_id', '!=', $leaderId)
            ->whereHas('dispositions', function ($q) use ($leaderId) {
                $q->where('from_leader_id', $leaderId)
                    ->where('status', 'disposisi');
            })
            ->orderBy('start_time')
            ->get();

        $dateFormatted = Carbon::parse($dateFilter)->locale('id')->translatedFormat('l, j F Y');

        $pdf = Pdf::loadView('activities.pdf', compact('dihadiri', 'didisposisi', 'leader', 'dateFormatted'));

        $fileName = 'Agenda_'.str_replace(' ', '_', $leader->short_name ?: $leader->name)."_{$dateFilter}.pdf";

        return $pdf->download($fileName);
    }

    /**
     * Export semua agenda pada tanggal tertentu dalam format tabel (tanpa filter kehadiran).
     */
    public function exportPdfTable(Request $request)
    {
        $dateFilter = $request->input('date', Carbon::today()->toDateString());
        $leaderId = $request->input('leader_id');

        if (! $leaderId) {
            return redirect()->back()->withErrors('Pimpinan harus dipilih untuk mencetak PDF.');
        }

        $leader = Leader::findOrFail($leaderId);

        $activities = Activity::with(['leader', 'location', 'organization', 'protocolOfficer', 'companions'])
            ->whereDate('activity_date', $dateFilter)
            ->whereIn('status', ['approved', 'submitted', 'draft', 'revision'])
            ->where(function ($q) use ($leaderId) {
                // Agenda yang dimiliki leader, atau agenda di mana leader pernah terlibat sebagai original leader
                $q->where('leader_id', $leaderId)
                    ->orWhere('original_leader_id', $leaderId);
            })
            ->orderBy('start_time')
            ->get();

        $dateFormatted = Carbon::parse($dateFilter)->locale('id')->translatedFormat('l, j F Y');

        $pdf = Pdf::loadView('activities.pdf-table', compact('activities', 'leader', 'dateFormatted'))
            ->setPaper('a4', 'landscape');

        $fileName = 'Tabel_Agenda_'.str_replace(' ', '_', $leader->short_name ?: $leader->name)."_{$dateFilter}.pdf";

        return $pdf->download($fileName);
    }

    /**
     * Export agenda ke format teks WhatsApp (JSON response).
     * Struktur mirip PDF: hanya agenda yang dikonfirmasi hadir + yang didisposisi.
     */
    public function exportWhatsapp(Request $request)
    {
        $dateFilter = $request->input('date', Carbon::today()->toDateString());
        $leaderId = $request->input('leader_id');

        if (! $leaderId) {
            return response()->json(['error' => 'Pimpinan harus dipilih.'], 422);
        }

        $leader = Leader::findOrFail($leaderId);

        $dihadiri = Activity::with(['leader', 'location', 'organization', 'protocolOfficer', 'companions'])
            ->whereDate('activity_date', $dateFilter)
            ->whereIn('status', ['approved', 'submitted', 'draft', 'revision'])
            ->where('leader_id', $leaderId)
            // ->whereHas('dispositions', function ($q) use ($leaderId) {
            //     $q->where('from_leader_id', $leaderId)->where('status', 'hadir');
            // })
            ->orderBy('start_time')
            ->get();

        $didisposisi = Activity::with(['leader', 'location', 'organization', 'protocolOfficer', 'companions'])
            ->whereDate('activity_date', $dateFilter)
            ->whereIn('status', ['approved', 'submitted'])
            ->where('leader_id', '!=', $leaderId)
            ->whereHas('dispositions', function ($q) use ($leaderId) {
                $q->where('from_leader_id', $leaderId)->where('status', 'disposisi');
            })
            ->orderBy('start_time')
            ->get();

        $dateFormatted = Carbon::parse($dateFilter)->locale('id')->translatedFormat('l, j F Y');

        $posLower = strtolower($leader->position ?? '');
        if (str_contains($posLower, 'wakil') || str_contains($posLower, 'wabup')) {
            $leaderTitle = 'Wabup';
        } elseif (str_contains($posLower, 'bupati')) {
            $leaderTitle = 'Bupati';
        } elseif (str_contains($posLower, 'sekda')) {
            $leaderTitle = 'Sekda';
        } else {
            $leaderTitle = ucwords($posLower);
        }

        $formatActivity = function (Activity $activity, int $no, int $total): array {
            $rows = [];

            if ($activity->is_tentative || ! $activity->start_time) {
                $timeStr = 'Tentatif';
            } else {
                $start = Carbon::parse($activity->start_time);
                $end = $activity->end_time ? Carbon::parse($activity->end_time) : null;
                $dur = $end ? $start->diffInMinutes($end) : null;
                $tStr = 'Pkl. '.$start->format('H.i');
                $tStr .= $end ? '-'.$end->format('H.i').' WIB' : ' WIB';
                $tStr .= $dur ? " ({$dur}')" : '';
                $timeStr = $tStr;
            }

            $prefix = $total > 1 ? "({$no}). " : '';
            $location = $activity->location_id
                ? ($activity->location?->name ?? '-')
                : ($activity->location_text ?: '-');
            $organizer = $activity->organization_id
                ? ($activity->organization?->name ?? '-')
                : ($activity->organizer_text ?: '-');
            $companions = $activity->companions->count() > 0
                ? $activity->companions->pluck('position')
                    ->map(fn ($p) => Str::title($p))
                    ->join(', ')
                : null;

            $rows[] = "{$prefix}{$timeStr}";
            $rows[] = "*{$activity->title}*";
            $rows[] = "Lokasi: {$location}";
            $rows[] = "Penyelenggara: {$organizer}";

            if ($activity->dress_code) {
                $rows[] = "Pakaian: {$activity->dress_code}";
            }
            if ($companions) {
                $rows[] = "Pendamping: {$companions}";
            }
            if ($activity->contact_person_name) {
                $cp = "Narahubung: {$activity->contact_person_name}";
                $rows[] = $cp;

                if ($activity->contact_person_phone) {
                    // $cp .= " ({$activity->contact_person_phone})";
                    $rows[] = "No. HP: {$activity->contact_person_phone}";
                }
            }
            if ($activity->protocolOfficer) {
                $prot = "PIC Protokol: {$activity->protocolOfficer->name}";
                $rows[] = $prot;

                if ($activity->protocolOfficer->phone) {
                    // $prot .= " ({$activity->protocolOfficer->phone})";
                    $rows[] = "No. HP: {$activity->protocolOfficer->phone}";
                }
            }

            return $rows;
        };

        $lines = [];
        $lines[] = '*AGENDA KEGIATAN '.strtoupper($leader->position).'*';
        $lines[] = $dateFormatted;
        // $lines[] = str_repeat('━', 30);

        if ($dihadiri->isNotEmpty()) {
            foreach ($dihadiri as $i => $activity) {
                $lines[] = '';
                foreach ($formatActivity($activity, $i + 1, $dihadiri->count()) as $row) {
                    $lines[] = $row;
                }
                if ($i < $dihadiri->count() - 1) {
                    // $lines[] = str_repeat('─', 20);
                }
            }
        }

        if ($didisposisi->isNotEmpty()) {
            $lines[] = '';
            // $lines[] = str_repeat('━', 30);
            $lines[] = '*DISPOSISI:*';

            foreach ($didisposisi as $i => $activity) {
                $lines[] = '';
                $dispRows = $formatActivity($activity, $i + 1, $didisposisi->count());
                $lines[] = $dispRows[0]; // waktu
                $lines[] = $dispRows[1]; // judul
                $lines[] = '↩ Disposisi ke: '.($activity->leader?->position ?? '-');
                foreach (array_slice($dispRows, 2) as $row) {
                    $lines[] = $row;
                }
                if ($i < $didisposisi->count() - 1) {
                    $lines[] = str_repeat('─', 20);
                }
            }
        }

        if ($dihadiri->isEmpty() && $didisposisi->isEmpty()) {
            $lines[] = '';
            $lines[] = '_Tidak ada agenda kegiatan pada tanggal ini._';
        }

        $allActivities = $dihadiri->concat($didisposisi);
        $adcNames = $allActivities->pluck('adc')->filter()->unique()->join(', ');

        $lines[] = '';
        // $lines[] = str_repeat('━', 30);
        $lines[] = 'Adc: '.($adcNames ?: '-');
        $lines[] = '';
        $lines[] = 'Demikian Disampaikan';
        $lines[] = 'Hatur Nuhun';
        $lines[] = '';
        $lines[] = 'Salam';
        $lines[] = "*Tim Sekpri {$leaderTitle}*";

        return response()->json(['text' => implode("\n", $lines)]);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $leaderFilter = $request->input('leader');
        $dateFilter = $request->input('date', Carbon::today()->toDateString());
        $statusFilter = $request->input('status');
        $user = auth()->user();
        if ($user->leader_id && $user->leader) {
            $leaderFilter = $user->leader->position;
        }
        if (! auth()->user()->leader_id && ! $leaderFilter) {
            return view('activities.select-leader', compact('dateFilter'));
        }

        $activities = $this->queryService->getPaginatedActivities(10, $search, $leaderFilter, $dateFilter, $statusFilter);
        $timelineData = $this->queryService->getTimelineData($dateFilter, null, $leaderFilter); // Status removed

        $leaders = Leader::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $protocolOfficers = ProtocolOfficer::where('is_active', true)->orderBy('name')->get();

        $initialView = $request->input('view', 'table');
        if (! in_array($initialView, ['table', 'card', 'timeline'], true)) {
            $initialView = 'table';
        }

        $viewData = compact('activities', 'timelineData', 'search', 'leaderFilter', 'dateFilter', 'statusFilter', 'leaders', 'locations', 'organizations', 'protocolOfficers', 'initialView');

        if ($request->ajax()) {
            return response()->view('activities._content', $viewData);
        }

        return view('activities.index', $viewData);
    }

    public function create(Request $request)
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $leaderFilter = $request->query('leader');

        $leaders = Leader::where('is_active', true)->orderBy('hierarchy_level')->get();

        $user = auth()->user();
        $mainLeaders = $leaders->filter(function ($leader) use ($user) {
            if ($user->leader_id) {
                return $leader->id === $user->leader_id;
            }

            return in_array($leader->hierarchy_level, [1, 2, 3]);
        });

        $defaultLeaderId = null;
        if ($user->leader_id) {
            $defaultLeaderId = $user->leader_id;
        } elseif ($leaderFilter) {
            $matched = $mainLeaders->first(fn ($l) => strtolower($l->position) === strtolower($leaderFilter));
            if ($matched) {
                $defaultLeaderId = $matched->id;
            }
        }

        $locations = Location::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $protocolOfficers = ProtocolOfficer::where('is_active', true)->orderBy('name')->get();
        $dressCodes = DressCode::where('is_active', true)->orderBy('name')->pluck('name');

        return view('activities.create', compact('leaders', 'mainLeaders', 'locations', 'organizations', 'protocolOfficers', 'dressCodes', 'date', 'defaultLeaderId', 'leaderFilter'));
    }

    public function store(StoreActivityRequest $request)
    {
        $data = $request->validated();
        $data['is_disposition'] = $request->boolean('is_disposition');
        $data['is_tentative'] = $request->boolean('is_tentative');
        if ($data['is_tentative']) {
            $data['start_time'] = null;
            $data['end_time'] = null;
        }

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

        $data['companion_ids'] = $request->input('companion_ids', []);

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

        return redirect()->route('activities.index', array_filter([
            'date' => $request->query('date'),
            'leader' => $request->query('leader'),
        ]))->with('success', $msg);
    }

    public function show(Request $request, Activity $activity)
    {
        $activity->load(['leader', 'companions', 'location', 'organization', 'protocolOfficer']);

        $activityLogs = \Spatie\Activitylog\Models\Activity::where('subject_type', Activity::class)
            ->where('subject_id', $activity->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $dateFilter = $request->query('date');
        $leaderFilter = $request->query('leader');

        return view('activities.show', compact('activity', 'activityLogs', 'dateFilter', 'leaderFilter'));
    }

    public function edit(Request $request, Activity $activity)
    {
        $leaders = Leader::where('is_active', true)->orderBy('hierarchy_level')->get();

        $user = auth()->user();
        $mainLeaders = $leaders->filter(function ($leader) use ($user) {
            if ($user->leader_id) {
                return $leader->id === $user->leader_id;
            }

            return in_array($leader->hierarchy_level, [1, 2, 3]);
        });

        $locations = Location::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $protocolOfficers = ProtocolOfficer::where('is_active', true)->orderBy('name')->get();
        $dressCodes = DressCode::where('is_active', true)->orderBy('name')->pluck('name');

        $dateFilter = $request->query('date');
        $leaderFilter = $request->query('leader');

        return view('activities.edit', compact('activity', 'leaders', 'mainLeaders', 'locations', 'organizations', 'protocolOfficers', 'dressCodes', 'dateFilter', 'leaderFilter'));
    }

    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $data = $request->validated();
        $data['is_disposition'] = $request->boolean('is_disposition');
        $data['is_tentative'] = $request->boolean('is_tentative');
        if ($data['is_tentative']) {
            $data['start_time'] = null;
            $data['end_time'] = null;
        }

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

        $data['companion_ids'] = $request->input('companion_ids', []);

        $this->service->updateActivity($activity, $data);

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Agenda berhasil diperbarui.');
        }

        return redirect()->route('activities.index', array_filter([
            'date' => $request->query('date'),
            'leader' => $request->query('leader'),
        ]))->with('success', 'Agenda berhasil diperbarui.');
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
        $roleName = $user->roles->first()?->name ?? '';
        $isAjudan = str_contains($roleName, 'ajudan_');
        $isSekpri = str_contains($roleName, 'sekpri_');
        $isOwnLeader = $user->leader_id === $activity->leader_id;

        if (! $isCreator && ! $isAjudan && ! ($isSekpri && $isOwnLeader)) {
            abort(403, 'Anda tidak diizinkan untuk menghapus agenda ini.');
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
