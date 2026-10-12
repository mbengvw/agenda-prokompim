<?php

namespace App\Services;

use App\Models\Activity;
use App\Repositories\ActivityRepository;
use Illuminate\Support\Facades\DB;

class ActivityService
{
    public function __construct(protected ActivityRepository $repository)
    {
        //
    }

    public function createActivity(array $data): Activity
    {
        return DB::transaction(function () use ($data) {
            if (! isset($data['status'])) {
                $data['status'] = 'draft';
            }

            if (isset($data['leader_id'])) {
                $data['original_leader_id'] = $data['leader_id'];
            }

            $companionIds = $data['companion_ids'] ?? [];
            unset($data['companion_ids']);

            $activity = $this->repository->create($data);

            if ($activity->parent_activity_id === null) {
                $activity->companions()->sync($companionIds);
                $activity->refresh();
                $this->syncCompanionActivities($activity, $companionIds);
            }

            return $activity;
        });
    }

    public function updateActivity(Activity $activity, array $data): bool
    {
        return DB::transaction(function () use ($activity, $data) {
            $hasCompanionKey = array_key_exists('companion_ids', $data);
            $companionIds = $data['companion_ids'] ?? [];
            unset($data['companion_ids']);

            $updated = $this->repository->update($activity, $data);

            if ($activity->parent_activity_id === null) {
                if ($hasCompanionKey) {
                    $activity->companions()->sync($companionIds);
                    $activity->refresh();
                    $this->syncCompanionActivities($activity, $companionIds);
                } else {
                    $existingCompanionIds = $activity->companions()->pluck('leaders.id')->toArray();
                    $activity->refresh();
                    $this->syncCompanionActivities($activity, $existingCompanionIds);
                }
            }

            return $updated;
        });
    }

    public function updateStatus(Activity $activity, string $status, ?string $revisionNotes = null): bool
    {
        return DB::transaction(function () use ($activity, $status, $revisionNotes) {
            $data = ['status' => $status];
            if ($revisionNotes !== null) {
                $data['revision_notes'] = $revisionNotes;
            }

            $updated = $this->repository->update($activity, $data);

            if ($updated && $activity->parent_activity_id === null) {
                $activity->loadMissing('childActivities');
                foreach ($activity->childActivities as $child) {
                    $this->repository->update($child, $data);
                }
            }

            return $updated;
        });
    }

    public function deleteActivity(Activity $activity): bool
    {
        return DB::transaction(function () use ($activity) {
            if ($activity->parent_activity_id !== null) {
                $parent = $activity->parentActivity;
                if ($parent && $activity->leader_id) {
                    $parent->companions()->detach($activity->leader_id);
                }
            } else {
                $activity->loadMissing('childActivities');
                foreach ($activity->childActivities as $child) {
                    $this->repository->delete($child);
                }
            }

            return $this->repository->delete($activity);
        });
    }

    protected function syncCompanionActivities(Activity $activity, array $companionIds): void
    {
        if ($activity->parent_activity_id !== null) {
            return;
        }

        $activity->loadMissing('leader');
        $mainLeader = $activity->leader;

        $targetLeaderIds = array_values(array_unique(array_filter(
            array_map('intval', $companionIds),
            fn (int $id) => $id > 0 && $id !== (int) $activity->leader_id
        )));

        $existingChildren = Activity::withTrashed()
            ->where('parent_activity_id', $activity->id)
            ->get();

        // 1. Delete child activities for companions that are no longer selected
        foreach ($existingChildren as $child) {
            if (! $child->trashed() && ! in_array((int) $child->leader_id, $targetLeaderIds, true)) {
                $this->repository->delete($child);
            }
        }

        $companionTitle = ($mainLeader && ! empty($mainLeader->position)
            ? 'Mendampingi '.$mainLeader->position.' dalam kegiatan '
            : 'Mendampingi kegiatan ').$activity->title;

        $childData = [
            'activity_request_id' => $activity->activity_request_id,
            'title' => $companionTitle,
            'description' => $activity->description,
            'activity_date' => $activity->getRawOriginal('activity_date') ?? $activity->activity_date,
            'start_time' => $activity->getRawOriginal('start_time') ?? $activity->start_time,
            'end_time' => $activity->getRawOriginal('end_time') ?? $activity->end_time,
            'is_tentative' => (bool) $activity->is_tentative,
            'location_id' => $activity->location_id,
            'location_text' => $activity->location_text,
            'organization_id' => $activity->organization_id,
            'organizer_text' => $activity->organizer_text,
            'contact_person_name' => $activity->contact_person_name,
            'contact_person_phone' => $activity->contact_person_phone,
            'adc' => $activity->adc,
            'is_disposition' => false,
            'disposition_to_id' => null,
            'protocol_officer_id' => $activity->protocol_officer_id,
            'dress_code' => $activity->dress_code,
            'status' => $activity->status,
            'notes' => $activity->notes,
        ];

        // 2. Create or update/restore child activities for current companions
        foreach ($targetLeaderIds as $leaderId) {
            $childrenForLeader = $existingChildren->where('leader_id', $leaderId);
            $child = $childrenForLeader->first();

            // Clean up any extraneous duplicate child records for the same leader if any
            if ($childrenForLeader->count() > 1) {
                foreach ($childrenForLeader->skip(1) as $extraChild) {
                    if (! $extraChild->trashed()) {
                        $this->repository->delete($extraChild);
                    }
                }
            }

            if ($child) {
                if ($child->trashed()) {
                    $this->repository->restore($child);
                }
                $this->repository->update($child, $childData);
            } else {
                $newChildData = array_merge($childData, [
                    'leader_id' => $leaderId,
                    'original_leader_id' => $leaderId,
                    'parent_activity_id' => $activity->id,
                ]);
                $this->repository->create($newChildData);
            }
        }
    }
}
