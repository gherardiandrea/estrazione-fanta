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

    public function recentDrawHistory(int $limit = 12): array
    {
        return $this->draws()
            ->latest('id')
            ->limit($limit)
            ->get(['team_name', 'draw_number', 'completed_cycles'])
            ->map(static fn (ExtractionDraw $draw): array => [
                'team' => $draw->team_name,
                'drawNumber' => $draw->draw_number,
                'completedCycles' => $draw->completed_cycles,
            ])
            ->all();
    }
}
