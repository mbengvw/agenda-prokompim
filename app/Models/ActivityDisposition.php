<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityDisposition extends Model
{
    protected $fillable = [
        'activity_id',
        'from_leader_id',
        'to_leader_id',
        'status',
        'notes',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function fromLeader(): BelongsTo
    {
        return $this->belongsTo(Leader::class, 'from_leader_id');
    }

    public function toLeader(): BelongsTo
    {
        return $this->belongsTo(Leader::class, 'to_leader_id');
    }
}
