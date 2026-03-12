<?php

namespace App\Actions\TeamDraw;

use App\Models\ExtractionConfig;
use App\Services\TeamDrawService;

class ResetTeamCycleAction
{
    public function __construct(private readonly TeamDrawService $drawService)
    {
    }

    public function execute(ExtractionConfig $config): array
    {
        if ($config->draw_number > 0) {
            $currentCycleDrawIds = $config->draws()
                ->latest('id')
                ->limit($config->draw_number)
                ->pluck('id');

            if ($currentCycleDrawIds->isNotEmpty()) {
                $config->draws()->whereIn('id', $currentCycleDrawIds)->delete();
            }
        }

        $completedCycles = (int) ($config->draws()->max('completed_cycles') ?? 0);
        $resetState = $this->drawService->resetState($config->teams);

        $config->update([
            'last_team' => null,
            'remaining_teams' => $resetState['remainingTeams'],
            'draw_number' => $resetState['drawNumber'],
            'completed_cycles' => $completedCycles,
        ]);

        return array_merge([
            'remainingTeams' => $resetState['remainingTeams'],
            'completedCycles' => $completedCycles,
        ], $config->historyPayload());
    }
}
