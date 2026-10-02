<?php

namespace App\QueryServices;

use App\Models\Activity;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityQueryService
{
    public function __construct(protected Activity $model)
    {
        //
    }

    public function getPaginatedActivities(int $perPage = 10, ?string $search = null, ?string $status = null, ?string $date = null): LengthAwarePaginator
    {
        $query = $this->model->with(['protocolOfficer', 'location', 'organization'])->latest('activity_date')->latest('start_time');

        if ($date) {
            $query->whereDate('activity_date', $date);
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

    public function getTimelineData(string $date, ?string $status = null): array
    {
        $timelineStartHour = 4;
        $timelineEndHour = 22;
        $totalMinutes = ($timelineEndHour - $timelineStartHour) * 60;

        $timelineData = [];
        $activeLeaders = \App\Models\Leader::where('is_active', true)->orderBy('id')->get();

        $timelineActivities = $this->model->with(['companions', 'location', 'organization'])
            ->whereDate('activity_date', $date)
            ->when($status, fn($q) => $q->where('status', $status))
            ->get();

        foreach ($activeLeaders as $leader) {
            $leaderActivities = [];
            foreach ($timelineActivities as $act) {
                $isPrimary = $act->leader_id === $leader->id;
                $isCompanion = $act->companions->contains('id', $leader->id);
                
                if ($isPrimary || $isCompanion) {
                    $start = clone $act->start_time; // It's cast to datetime usually, wait, it's cast as datetime?
                    $start = \Carbon\Carbon::parse($act->start_time);
                    $end = $act->end_time ? \Carbon\Carbon::parse($act->end_time) : $start->copy()->addHour();
                    
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
                            'end_time' => $end->format('H:i')
                        ];
                    }
                }
            }
            
            usort($leaderActivities, fn($a, $b) => $a['left'] <=> $b['left']);
            
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
                if (!$placed) {
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
            'data' => $timelineData
        ];
    }
}
