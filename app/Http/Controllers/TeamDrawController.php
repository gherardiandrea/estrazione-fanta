<?php

namespace App\Http\Controllers;

use App\Actions\TeamDraw\ClearConfigurationAction;
use App\Actions\TeamDraw\ClearDrawHistoryAction;
use App\Actions\TeamDraw\DrawTeamAction;
use App\Actions\TeamDraw\ResetTeamCycleAction;
use App\Actions\TeamDraw\SetupConfigurationAction;
use App\Models\ExtractionConfig;
use App\Http\Requests\StoreTeamSetupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TeamDrawController extends Controller
{
    private const CONFIG_TOKEN_KEY = 'extractionConfigToken';

    public function __construct(
        private readonly SetupConfigurationAction $setupConfiguration,
        private readonly DrawTeamAction $drawTeam,
        private readonly ResetTeamCycleAction $resetTeamCycle,
        private readonly ClearConfigurationAction $clearConfiguration,
        private readonly ClearDrawHistoryAction $clearDrawHistory,
    )
    {
    }

    public function index(Request $request)
    {
        $defaultTeams = config('teams.list', []);
        $config = $this->resolveConfig();
        $needsSetup = !$config;

        if ($needsSetup) {
            return view('draw', [
                'needsSetup' => true,
                'defaultTeams' => $defaultTeams,
                'lastDrawnTeam' => 'Nessuna squadra estratta',
                'drawNumber' => 0,
                'completedCycles' => 0,
                'remainingTeams' => [],
                'drawHistory' => [],
                'historyByCycle' => [],
                'historyCycles' => [],
                'selectedHistoryCycle' => null,
            ]);
        }

        $requestedCycle = $request->query('history_cycle');
        $selectedCycle = is_numeric($requestedCycle) ? (int) $requestedCycle : null;
        $history = $config->historyPayload($selectedCycle);

        return view('draw', [
            'lastDrawnTeam' => $config->last_team ?? 'Nessuna squadra estratta',
            'drawNumber' => $config->draw_number,
            'completedCycles' => $config->completed_cycles,
            'remainingTeams' => $config->remaining_teams,
            'drawHistory' => $history['drawHistory'],
            'historyByCycle' => $history['historyByCycle'],
            'historyCycles' => $history['historyCycles'],
            'selectedHistoryCycle' => $history['selectedHistoryCycle'],
            'needsSetup' => false,
            'defaultTeams' => $defaultTeams,
        ]);
    }

    public function setup(StoreTeamSetupRequest $request)
    {
        $this->setupConfiguration->execute($request->teams());

        return redirect('/');
    }

    public function draw()
    {
        $config = $this->resolveConfig();

        if (!$config) {
            return response()->json([
                'message' => 'Configura prima l\'elenco squadre.',
            ], 422);
        }

        return response()->json($this->drawTeam->execute($config));
    }

    public function reset()
    {
        $config = $this->resolveConfig();

        if (!$config) {
            return response()->json([
                'message' => 'Configura prima l\'elenco squadre.',
            ], 422);
        }

        return response()->json($this->resetTeamCycle->execute($config));
    }

    public function newConfiguration()
    {
        $this->clearConfiguration->execute($this->resolveConfig());

        return redirect('/');
    }

    public function clearHistory()
    {
        $config = $this->resolveConfig();

        if (!$config) {
            return response()->json([
                'message' => 'Configura prima l\'elenco squadre.',
            ], 422);
        }

        return response()->json($this->clearDrawHistory->execute($config));
    }

    private function resolveConfig(): ?ExtractionConfig
    {
        $token = Session::get(self::CONFIG_TOKEN_KEY);

        if (!$token) {
            return null;
        }

        return ExtractionConfig::where('token', $token)->first();
    }
}
