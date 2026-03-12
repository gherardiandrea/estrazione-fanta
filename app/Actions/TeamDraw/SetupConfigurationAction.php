<?php

namespace App\Actions\TeamDraw;

use App\Models\ExtractionConfig;
use App\Services\TeamDrawService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SetupConfigurationAction
{
    private const CONFIG_TOKEN_KEY = 'extractionConfigToken';

    public function __construct(private readonly TeamDrawService $drawService)
    {
    }

    public function execute(array $teams): void
    {
        $token = Session::get(self::CONFIG_TOKEN_KEY, (string) Str::uuid());
        $resetState = $this->drawService->resetState($teams);

        $config = ExtractionConfig::firstOrNew(['token' => $token]);
        $config->fill([
            'teams' => $teams,
            'remaining_teams' => $resetState['remainingTeams'],
            'last_team' => null,
            'draw_number' => $resetState['drawNumber'],
            'completed_cycles' => $resetState['completedCycles'],
        ]);

        $config->save();
        $config->draws()->delete();

        Session::put(self::CONFIG_TOKEN_KEY, $token);
    }
}
