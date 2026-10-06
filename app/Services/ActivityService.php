<?php

namespace App\Services;

use App\Models\Activity;
use App\Repositories\ActivityRepository;

class ActivityService
{
    public function __construct(protected ActivityRepository $repository)
    {
        //
    }

    public function createActivity(array $data): Activity
    {
        if (! isset($data['status'])) {
            $data['status'] = 'draft';
        }
        
        if (isset($data['leader_id'])) {
            $data['original_leader_id'] = $data['leader_id'];
        }

        $companionIds = $data['companion_ids'] ?? [];
        unset($data['companion_ids']);

        $activity = $this->repository->create($data);

        if (! empty($companionIds)) {
            $activity->companions()->sync($companionIds);

            // Create child activities for companions
            $mainLeader = \App\Models\Leader::find($activity->leader_id);
            if ($mainLeader) {
                foreach ($companionIds as $companionId) {
                    $companionData = $data;
                    $companionData['leader_id'] = $companionId;
                    $companionData['original_leader_id'] = $companionId;
                    $companionData['parent_activity_id'] = $activity->id;
                    $companionData['title'] = "Mendampingi " . $mainLeader->position . " dalam " . $activity->title;
                    
                    $this->repository->create($companionData);
                }
            }
        }

        return $activity;
    }

    public function updateActivity(Activity $activity, array $data): bool
    {
        $companionIds = $data['companion_ids'] ?? [];
        unset($data['companion_ids']);

        $updated = $this->repository->update($activity, $data);

        $activity->companions()->sync($companionIds);

        return $updated;
    }

    public function updateStatus(Activity $activity, string $status, ?string $revisionNotes = null): bool
    {
        $data = ['status' => $status];
        if ($revisionNotes !== null) {
            $data['revision_notes'] = $revisionNotes;
        }

        return $this->repository->update($activity, $data);
    }

    public function deleteActivity(Activity $activity): bool
    {
        return $this->repository->delete($activity);
    }
}
