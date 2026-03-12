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
        $resetState = $this->drawService->resetState($config->teams);

        $config->update([
            'last_team' => null,
            'remaining_teams' => $resetState['remainingTeams'],
            'draw_number' => $resetState['drawNumber'],
            'completed_cycles' => $resetState['completedCycles'],
        ]);

        return array_merge([
            'remainingTeams' => $resetState['remainingTeams'],
        ], $config->historyPayload());
    }
}
