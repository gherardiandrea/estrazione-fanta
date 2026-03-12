<?php

namespace App\Http\Controllers;

use App\Models\ExtractionConfig;
use App\Http\Requests\StoreTeamSetupRequest;
use Illuminate\Support\Facades\Session;
use App\Services\TeamDrawService;
use Illuminate\Support\Str;

class TeamDrawController extends Controller
{
    private const CONFIG_TOKEN_KEY = 'extractionConfigToken';

    public function __construct(private readonly TeamDrawService $extractor)
    {
    }

    public function index()
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
            ]);
        }

        return view('draw', [
            'lastDrawnTeam' => $config->last_team ?? 'Nessuna squadra estratta',
            'drawNumber' => $config->draw_number,
            'completedCycles' => $config->completed_cycles,
            'remainingTeams' => $config->remaining_teams,
            'drawHistory' => $config->recentDrawHistory(),
            'needsSetup' => false,
            'defaultTeams' => $defaultTeams,
        ]);
    }

    public function setup(StoreTeamSetupRequest $request)
    {
        $teams = $request->teams();

        $token = Session::get(self::CONFIG_TOKEN_KEY, (string) Str::uuid());
        $resetState = $this->extractor->resetState($teams);
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

        $result = $this->extractor->extract(
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

        return response()->json([
            'team' => $result['team'],
            'drawNumber' => $result['drawNumber'],
            'completedCycles' => $result['completedCycles'],
            'remainingTeams' => $result['remainingTeams'],
            'drawHistory' => $config->recentDrawHistory(),
        ]);
    }

    public function reset()
    {
        $config = $this->resolveConfig();

        if (!$config) {
            return response()->json([
                'message' => 'Configura prima l\'elenco squadre.',
            ], 422);
        }

        $resetState = $this->extractor->resetState($config->teams);

        $config->update([
            'last_team' => null,
            'remaining_teams' => $resetState['remainingTeams'],
            'draw_number' => $resetState['drawNumber'],
            'completed_cycles' => $resetState['completedCycles'],
        ]);

        return response()->json([
            'remainingTeams' => $resetState['remainingTeams'],
            'drawHistory' => $config->recentDrawHistory(),
        ]);
    }

    public function newConfiguration()
    {
        $config = $this->resolveConfig();
        if ($config) {
            $config->delete();
        }

        Session::forget(self::CONFIG_TOKEN_KEY);

        return redirect('/');
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
