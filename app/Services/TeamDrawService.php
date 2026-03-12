<?php

namespace App\Services;

class TeamDrawService
{
    public function initialState(array $teams): array
    {
        return [
            'lastDrawnTeam' => 'Nessuna squadra estratta',
            'drawNumber' => 0,
            'completedCycles' => 0,
            'remainingTeams' => $teams,
        ];
    }

    public function extract(array $remainingTeams, int $drawNumber, int $completedCycles, array $baseTeams): array
    {
        if (empty($remainingTeams)) {
            $remainingTeams = $baseTeams;
            $completedCycles++;
            $drawNumber = 0;
        }

        $drawnKey = array_rand($remainingTeams);
        $drawnTeam = $remainingTeams[$drawnKey];

        unset($remainingTeams[$drawnKey]);

        $drawNumber++;

        return [
            'team' => $drawnTeam,
            'drawNumber' => $drawNumber,
            'completedCycles' => $completedCycles,
            'remainingTeams' => array_values($remainingTeams),
        ];
    }

    public function resetState(array $teams): array
    {
        return [
            'lastDrawnTeam' => 'Nessuna squadra estratta',
            'drawNumber' => 0,
            'completedCycles' => 0,
            'remainingTeams' => $teams,
        ];
    }
}
