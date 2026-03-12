<?php

namespace App\Actions\TeamDraw;

use App\Models\ExtractionConfig;
use App\Services\TeamDrawService;

class DrawTeamAction
{
    public function __construct(private readonly TeamDrawService $drawService)
    {
    }

    public function execute(ExtractionConfig $config): array
    {
        $result = $this->drawService->extract(
            $config->remaining_teams,
            $config->draw_number,
            $config->completed_cycles,
            $config->teams
        );

        $config->update([
            'last_team' => $result['team'],
            'draw_number' => $result['drawNumber'],
            'completed_cycles' => $result['completedCycles'],
            'remaining_teams' => $result['remainingTeams'],
        ]);

        $config->draws()->create([
            'team_name' => $result['team'],
            'draw_number' => $result['drawNumber'],
            'completed_cycles' => $result['completedCycles'],
        ]);

        return array_merge([
            'team' => $result['team'],
            'drawNumber' => $result['drawNumber'],
            'completedCycles' => $result['completedCycles'],
            'remainingTeams' => $result['remainingTeams'],
        ], $config->historyPayload());
    }
}
