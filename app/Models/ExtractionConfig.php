<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtractionConfig extends Model
{
    protected $fillable = [
        'token',
        'teams',
        'remaining_teams',
        'last_team',
        'draw_number',
        'completed_cycles',
    ];

    protected $casts = [
        'teams' => 'array',
        'remaining_teams' => 'array',
        'draw_number' => 'integer',
        'completed_cycles' => 'integer',
    ];
}
