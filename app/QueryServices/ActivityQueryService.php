<?php

namespace App\QueryServices;

use App\Models\Activity;
use App\Models\Leader;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityQueryService
{
    public function __construct(protected Activity $model)
    {
        //
    }

    public function getPaginatedActivities(int $perPage = 10, ?string $search = null, ?string $leader = null, ?string $date = null, ?string $status = null): LengthAwarePaginator
    {
        $query = $this->model->with(['protocolOfficer', 'location', 'organization', 'companions', 'leader', 'originalLeader', 'dispositions']);

        if ($date) {
            $query->whereDate('activity_date', $date)
                ->orderBy('start_time', 'asc');
        } else {
            $query->orderBy('activity_date', 'asc')
                ->orderBy('start_time', 'asc');
        }

        if ($leader) {
            $searchLeader = strtolower($leader);
            $query->where(function ($q) use ($searchLeader) {
                $q->whereHas('leader', function ($q2) use ($searchLeader) {
                    $q2->whereRaw('LOWER(position) = ?', [$searchLeader]);
                })->orWhereHas('originalLeader', function ($q2) use ($searchLeader) {
                    $q2->whereRaw('LOWER(position) = ?', [$searchLeader]);
                });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(location_text) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getTimelineData(string $date, ?string $status = null, ?string $leaderFilter = null): array
    {
        $timelineStartHour = 4;
        $timelineEndHour = 22;
        $totalMinutes = ($timelineEndHour - $timelineStartHour) * 60;

        $timelineData = [];
        $activeLeaders = Leader::where('is_active', true)
            ->when($leaderFilter, function ($query) use ($leaderFilter) {
                $query->whereRaw('LOWER(position) = ?', [strtolower($leaderFilter)]);
            })
            ->get()->map(function ($leader) {
            $pos = strtolower($leader->position);
            if (str_contains($pos, 'bupati') && ! str_contains($pos, 'wakil')) {
                $leader->level = 1;
            } elseif (str_contains($pos, 'wakil bupati')) {
                $leader->level = 2;
            } elseif (str_contains($pos, 'sekda') || str_contains($pos, 'sekretaris daerah')) {
                $leader->level = 3;
            } else {
                $leader->level = 4;
            }

            return $leader;
        })->filter(function ($leader) {
            return $leader->level < 4;
        })->sortBy('level')->values();

        $query = $this->model->with(['companions', 'location', 'organization', 'leader'])
            ->whereDate('activity_date', $date)
            ->when($status, fn ($q) => $q->where('status', $status));

        $timelineActivities = $query->get();

        foreach ($activeLeaders as $leader) {
            $leaderActivities = [];
            foreach ($timelineActivities as $act) {
                $isPrimary = $act->leader_id === $leader->id || $act->original_leader_id === $leader->id;
                $isCompanion = $act->companions->contains('id', $leader->id);

                if (($isPrimary || $isCompanion) && !$act->is_tentative) {
                    $start = clone $act->start_time; // It's cast to datetime usually, wait, it's cast as datetime?
                    $start = Carbon::parse($act->start_time);
                    $end = $act->end_time ? Carbon::parse($act->end_time) : $start->copy()->addHour();

                    $startMinutes = max(0, ($start->hour - $timelineStartHour) * 60 + $start->minute);
                    $endMinutes = min($totalMinutes, max($startMinutes + 30, ($end->hour - $timelineStartHour) * 60 + $end->minute));

                    $leftPct = ($startMinutes / $totalMinutes) * 100;
                    $widthPct = (($endMinutes - $startMinutes) / $totalMinutes) * 100;

                    if ($widthPct > 0) {
                        $leaderActivities[] = [
                            'activity' => $act,
                            'is_primary' => $isPrimary,
                            'left' => $leftPct,
                            'width' => $widthPct,
                            'start_time' => $start->format('H:i'),
                            'end_time' => $end->format('H:i'),
                        ];
                    }
                }
            }

            usort($leaderActivities, fn ($a, $b) => $a['left'] <=> $b['left']);

            $rows = [];
            foreach ($leaderActivities as &$la) {
                $placed = false;
                foreach ($rows as $rowIndex => $rowEndPct) {
                    if ($la['left'] >= $rowEndPct) {
                        $la['row'] = $rowIndex;
                        $rows[$rowIndex] = $la['left'] + $la['width'];
                        $placed = true;
                        break;
                    }
                }
                if (! $placed) {
                    $la['row'] = count($rows);
                    $rows[] = $la['left'] + $la['width'];
                }
            }
            unset($la);

            $timelineData[] = [
                'leader' => $leader,
                'activities' => $leaderActivities,
                'total_rows' => max(1, count($rows)),
                'has_conflict' => count($rows) > 1,
            ];
        }

        return [
            'start_hour' => $timelineStartHour,
            'end_hour' => $timelineEndHour,
            'data' => $timelineData,
        ];
    }
}
