<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\SquadraExtractorService;

class SquadraController extends Controller
{
    private const SESSION_KEYS = [
        'activeSquadre',
        'ultimaSquadraEstratta',
        'squadreRestanti',
        'numeroEstrazione',
        'cicliCompletati',
    ];

    public function __construct(private readonly SquadraExtractorService $extractor)
    {
    }

    public function index()
    {
        $defaultSquadre = config('squadre.list', []);
        $activeSquadre = Session::get('activeSquadre', []);
        $needsSetup = empty($activeSquadre);

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

        $initialState = $this->extractor->initialState($activeSquadre);

        $ultimaSquadraEstratta = Session::get('ultimaSquadraEstratta', $initialState['ultimaSquadraEstratta']);
        $squadreRestanti = Session::get('squadreRestanti', $initialState['squadreRestanti']);
        $numeroEstrazione = Session::get('numeroEstrazione', $initialState['numeroEstrazione']);
        $cicliCompletati = Session::get('cicliCompletati', $initialState['cicliCompletati']);

        return view('squadre', [
            'squadra' => $ultimaSquadraEstratta,
            'numeroEstrazione' => $numeroEstrazione,
            'cicliCompletati' => $cicliCompletati,
            'squadreRestanti' => $squadreRestanti,
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

        $resetState = $this->extractor->resetState($squadre);

        Session::put('activeSquadre', $squadre);
        Session::put('ultimaSquadraEstratta', $resetState['ultimaSquadraEstratta']);
        Session::put('squadreRestanti', $resetState['squadreRestanti']);
        Session::put('numeroEstrazione', $resetState['numeroEstrazione']);
        Session::put('cicliCompletati', $resetState['cicliCompletati']);

        return redirect('/');
    }

    public function estrai()
    {
        $activeSquadre = Session::get('activeSquadre', []);

        if (empty($activeSquadre)) {
            return response()->json([
                'message' => 'Configura prima l\'elenco squadre.',
            ], 422);
        }

        $initialState = $this->extractor->initialState($activeSquadre);

        $squadreRestanti = Session::get('squadreRestanti', $initialState['squadreRestanti']);
        $numeroEstrazione = Session::get('numeroEstrazione', $initialState['numeroEstrazione']);
        $cicliCompletati = Session::get('cicliCompletati', $initialState['cicliCompletati']);

        $result = $this->extractor->extract($squadreRestanti, $numeroEstrazione, $cicliCompletati, $activeSquadre);

        Session::put('ultimaSquadraEstratta', $result['squadra']);
        Session::put('numeroEstrazione', $result['numeroEstrazione']);
        Session::put('cicliCompletati', $result['cicliCompletati']);
        Session::put('squadreRestanti', $result['squadreRestanti']);

        return response()->json([
            'squadra' => $result['squadra'],
            'numeroEstrazione' => $result['numeroEstrazione'],
            'cicliCompletati' => $result['cicliCompletati'],
            'squadreRestanti' => $result['squadreRestanti'],
        ]);
    }

    public function reset()
    {
        $activeSquadre = Session::get('activeSquadre', []);

        if (empty($activeSquadre)) {
            return response()->json([
                'message' => 'Configura prima l\'elenco squadre.',
            ], 422);
        }

        $resetState = $this->extractor->resetState($activeSquadre);

        Session::put('ultimaSquadraEstratta', $resetState['ultimaSquadraEstratta']);
        Session::put('squadreRestanti', $resetState['squadreRestanti']);
        Session::put('numeroEstrazione', $resetState['numeroEstrazione']);
        Session::put('cicliCompletati', $resetState['cicliCompletati']);

        return response()->json([
            'squadreRestanti' => $resetState['squadreRestanti'],
        ]);
    }

    public function nuovaConfigurazione()
    {
        Session::forget(self::SESSION_KEYS);

        return redirect('/');
    }

    private function parseCustomTeams(string $rawInput): array
    {
        $chunks = preg_split('/[\r\n,;]+/', $rawInput) ?: [];
        $trimmed = array_map(static fn (string $item): string => trim($item), $chunks);
        $filtered = array_filter($trimmed, static fn (string $item): bool => $item !== '');

        return array_values(array_unique($filtered));
    }
}
