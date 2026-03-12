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

    public function historyByCycle(): array
    {
        $draws = $this->draws()
            ->orderBy('id')
            ->get(['team_name', 'draw_number', 'completed_cycles']);

        $historyByCycle = [];
        $cycle = 0;

        foreach ($draws as $index => $draw) {
            if ($index === 0 || $draw->draw_number === 1) {
                $cycle++;
            }

            $historyByCycle[$cycle][] = [
                'team' => $draw->team_name,
                'drawNumber' => $draw->draw_number,
                'completedCycles' => $draw->completed_cycles,
            ];
        }

        return $historyByCycle;
    }

    public function historyPayload(?int $selectedCycle = null): array
    {
        $historyByCycle = $this->historyByCycle();
        $historyCycles = array_map('intval', array_keys($historyByCycle));

        if (empty($historyCycles)) {
            return [
                'historyByCycle' => [],
                'historyCycles' => [],
                'selectedHistoryCycle' => null,
                'drawHistory' => [],
            ];
        }

        rsort($historyCycles);
        $defaultCycle = $historyCycles[0];
        $resolvedCycle = in_array($selectedCycle, $historyCycles, true) ? $selectedCycle : $defaultCycle;

        return [
            'historyByCycle' => $historyByCycle,
            'historyCycles' => $historyCycles,
            'selectedHistoryCycle' => $resolvedCycle,
            'drawHistory' => $historyByCycle[$resolvedCycle] ?? [],
        ];
    }
}
