<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'activity_request_id',
        'title',
        'description',
        'activity_date',
        'start_time',
        'end_time',
        'location_id',
        'location_text',
        'organization_id',
        'organizer_text',
        'leader_id',
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
    ];

    protected $casts = [
        'activity_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_disposition' => 'boolean',
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

    public function companions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Leader::class, 'activity_companions', 'activity_id', 'leader_id')->withTimestamps();
    }
}
