<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function draws(): HasMany
    {
        return $this->hasMany(ExtractionDraw::class, 'extraction_config_id');
    }
}
