<?php

namespace App\Http\Controllers;

use App\Models\ExtractionConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\SquadraExtractorService;
use Illuminate\Support\Str;

class SquadraController extends Controller
{
    private const CONFIG_TOKEN_KEY = 'extractionConfigToken';

    public function __construct(private readonly SquadraExtractorService $extractor)
    {
    }

    public function index()
    {
        $defaultSquadre = config('squadre.list', []);
        $config = $this->resolveConfig();
        $needsSetup = !$config;

        if ($needsSetup) {
            return view('squadre', [
                'needsSetup' => true,
                'defaultSquadre' => $defaultSquadre,
                'squadra' => 'Nessuna squadra estratta',
                'numeroEstrazione' => 0,
                'cicliCompletati' => 0,
                'squadreRestanti' => [],
            ]);
        }

        return view('squadre', [
            'squadra' => $config->last_team ?? 'Nessuna squadra estratta',
            'numeroEstrazione' => $config->draw_number,
            'cicliCompletati' => $config->completed_cycles,
            'squadreRestanti' => $config->remaining_teams,
            'needsSetup' => false,
            'defaultSquadre' => $defaultSquadre,
        ]);
    }

    public function setup(Request $request)
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:default,custom'],
            'custom_teams' => ['nullable', 'string'],
        ]);

        $squadre = $validated['mode'] === 'default'
            ? config('squadre.list', [])
            : $this->parseCustomTeams((string) ($validated['custom_teams'] ?? ''));

        if (count($squadre) < 2) {
            return back()
                ->withErrors(['custom_teams' => 'Inserisci almeno 2 squadre valide.'])
                ->withInput();
        }

        $token = Session::get(self::CONFIG_TOKEN_KEY, (string) Str::uuid());
        $resetState = $this->extractor->resetState($squadre);
        $config = ExtractionConfig::firstOrNew(['token' => $token]);

        $config->fill([
            'teams' => $squadre,
            'remaining_teams' => $resetState['squadreRestanti'],
            'last_team' => null,
            'draw_number' => $resetState['numeroEstrazione'],
            'completed_cycles' => $resetState['cicliCompletati'],
        ]);

        $config->save();

        Session::put(self::CONFIG_TOKEN_KEY, $token);

        return redirect('/');
    }

    public function estrai()
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
            'last_team' => $result['squadra'],
            'draw_number' => $result['numeroEstrazione'],
            'completed_cycles' => $result['cicliCompletati'],
            'remaining_teams' => $result['squadreRestanti'],
        ]);

        return response()->json([
            'squadra' => $result['squadra'],
            'numeroEstrazione' => $result['numeroEstrazione'],
            'cicliCompletati' => $result['cicliCompletati'],
            'squadreRestanti' => $result['squadreRestanti'],
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
            'remaining_teams' => $resetState['squadreRestanti'],
            'draw_number' => $resetState['numeroEstrazione'],
            'completed_cycles' => $resetState['cicliCompletati'],
        ]);

        return response()->json([
            'squadreRestanti' => $resetState['squadreRestanti'],
        ]);
    }

    public function nuovaConfigurazione()
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

    private function parseCustomTeams(string $rawInput): array
    {
        $chunks = preg_split('/[\r\n,;]+/', $rawInput) ?: [];
        $trimmed = array_map(static fn (string $item): string => trim($item), $chunks);
        $filtered = array_filter($trimmed, static fn (string $item): bool => $item !== '');

        return array_values(array_unique($filtered));
    }
}
