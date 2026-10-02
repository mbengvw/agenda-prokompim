<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProtocolOfficer extends Model
{
    protected $fillable = [
        'name',
        'employee_number',
        'phone',
        'email',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function activities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
