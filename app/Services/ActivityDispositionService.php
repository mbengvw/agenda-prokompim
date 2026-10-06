<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityDisposition;
use App\Models\Leader;
use App\Repositories\ActivityDispositionRepository;
use App\Repositories\ActivityRepository;
use Illuminate\Support\Facades\DB;

class ActivityDispositionService
{
    public function __construct(
        protected ActivityDispositionRepository $dispositionRepository,
        protected ActivityRepository $activityRepository
    ) {}

    /**
     * Handle disposition action from adjutant.
     *
     * @param  Activity  $activity  The activity being actioned on
     * @param  string  $status  The status (hadir, skip, disposisi)
     * @param  int|null  $fromLeaderId  The leader currently holding the activity
     * @param  int|null  $toLeaderId  The leader to whom the activity is disposed
     * @param  string|null  $notes  Additional notes from the adjutant
     */
    public function handleDisposition(
        Activity $activity,
        string $status,
        int $fromLeaderId,
        ?int $toLeaderId = null,
        ?string $notes = null
    ): ActivityDisposition|bool {
        return DB::transaction(function () use ($activity, $status, $fromLeaderId, $toLeaderId, $notes) {

            // Record the disposition history (update if already exists for this leader)
            $disposition = $this->dispositionRepository->updateOrCreate(
                [
                    'activity_id' => $activity->id,
                    'from_leader_id' => $fromLeaderId,
                ],
                [
                    'to_leader_id' => $toLeaderId,
                    'status' => $status,
                    'notes' => $notes,
                ]
            );

            // If it's a disposition to another leader, update the activity's main leader_id
            // so it appears in the target leader's dashboard.
            if ($status === 'disposisi' && $toLeaderId) {
                $this->activityRepository->update($activity, [
                    'leader_id' => $toLeaderId,
                    'is_disposition' => true,
                    'disposition_to_id' => $toLeaderId,
                ]);

                // Jika target disposisi sebelumnya adalah pendamping di kegiatan ini,
                // maka kita hapus kegiatan "Mendampingi" miliknya agar tidak duplikat.
                $companionActivity = Activity::where('parent_activity_id', $activity->id)
                    ->where('leader_id', $toLeaderId)
                    ->first();
                    
                if ($companionActivity) {
                    $this->activityRepository->delete($companionActivity);
                    $activity->companions()->detach($toLeaderId);
                }

            } else {
                // If cancelled (e.g. changed to hadir/skip), revert to the original leader if it was disposed
                if ($activity->is_disposition && $activity->leader_id != $fromLeaderId) {
                    $previousToLeaderId = $activity->leader_id;
                    
                    $this->activityRepository->update($activity, [
                        'leader_id' => $fromLeaderId,
                        'is_disposition' => false,
                        'disposition_to_id' => null,
                    ]);

                    // Kembalikan kegiatan "Mendampingi" yang sempat di-soft-delete jika ada
                    $trashedCompanionActivity = Activity::onlyTrashed()
                        ->where('parent_activity_id', $activity->id)
                        ->where('leader_id', $previousToLeaderId)
                        ->first();
                        
                    if ($trashedCompanionActivity) {
                        $trashedCompanionActivity->restore();
                        $activity->companions()->syncWithoutDetaching([$previousToLeaderId]);
                    }
                }
            }

            // Log activity manually for spatie/laravel-activitylog
            $logMessage = 'Ajudan mengkonfirmasi: '.strtoupper($status);
            if ($status === 'disposisi' && $toLeaderId) {
                $dispoTo = Leader::find($toLeaderId);
                if ($dispoTo) {
                    $logMessage .= ' kepada '.$dispoTo->name;
                }
            }
            if ($notes) {
                $logMessage .= ' (Catatan: '.$notes.')';
            }

            activity()
                ->performedOn($activity)
                ->causedBy(auth()->user())
                ->log($logMessage);

            return $disposition;
        });
    }
}
