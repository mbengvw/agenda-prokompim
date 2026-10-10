<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Activity extends Model
{
    use LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'activity_request_id',
        'title',
        'description',
        'activity_date',
        'start_time',
        'end_time',
        'is_tentative',
        'location_id',
        'location_text',
        'organization_id',
        'organizer_text',
        'leader_id',
        'original_leader_id',
        'contact_person_name',
        'contact_person_phone',
        'adc',
        'is_disposition',
        'disposition_to_id',
        'protocol_officer_id',
        'dress_code',
        'status',
        'notes',
        'revision_notes',
        'created_by',
        'parent_activity_id',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_disposition' => 'boolean',
        'is_tentative' => 'boolean',
    ];

    public function protocolOfficer(): BelongsTo
    {
        return $this->belongsTo(ProtocolOfficer::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Leader::class, 'leader_id');
    }

    public function companions(): BelongsToMany
    {
        return $this->belongsToMany(Leader::class, 'activity_companions', 'activity_id', 'leader_id')->withTimestamps();
    }

    public function dispositions(): HasMany
    {
        return $this->hasMany(ActivityDisposition::class);
    }

    public function dispositionTo(): BelongsTo
    {
        return $this->belongsTo(Leader::class, 'disposition_to_id');
    }

    public function originalLeader(): BelongsTo
    {
        return $this->belongsTo(Leader::class, 'original_leader_id');
    }

    public function getDurationAttribute(): string
    {
        if (! $this->start_time || ! $this->end_time) {
            return '-';
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        if ($end->lessThan($start)) {
            $end->addDay();
        }

        $diffInMinutes = $start->diffInMinutes($end);

        return $diffInMinutes > 0 ? $diffInMinutes.' Menit' : '-';
    }
}
