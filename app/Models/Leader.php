<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leader extends Model
{
    protected $fillable = [
        'name',
        'position',
        'short_name',
        'hierarchy_level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
