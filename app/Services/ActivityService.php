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

        $companionIds = $data['companion_ids'] ?? [];
        unset($data['companion_ids']);

        $activity = $this->repository->create($data);

        if (! empty($companionIds)) {
            $activity->companions()->sync($companionIds);
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
