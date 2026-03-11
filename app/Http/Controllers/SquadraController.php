<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\SquadraExtractorService;

class SquadraController extends Controller
{
    public function __construct(private readonly SquadraExtractorService $extractor)
    {
    }

    public function index()
    {
        $initialState = $this->extractor->initialState();

        $ultimaSquadraEstratta = Session::get('ultimaSquadraEstratta', $initialState['ultimaSquadraEstratta']);
        $squadreRestanti = Session::get('squadreRestanti', $initialState['squadreRestanti']);
        $numeroEstrazione = Session::get('numeroEstrazione', $initialState['numeroEstrazione']);
        $cicliCompletati = Session::get('cicliCompletati', $initialState['cicliCompletati']);

        return view('squadre', [
            'squadra' => $ultimaSquadraEstratta,
            'numeroEstrazione' => $numeroEstrazione,
            'cicliCompletati' => $cicliCompletati,
            'squadreRestanti' => $squadreRestanti
        ]);
    }

    public function estrai()
    {
        $initialState = $this->extractor->initialState();

        $squadreRestanti = Session::get('squadreRestanti', $initialState['squadreRestanti']);
        $numeroEstrazione = Session::get('numeroEstrazione', $initialState['numeroEstrazione']);
        $cicliCompletati = Session::get('cicliCompletati', $initialState['cicliCompletati']);

        $result = $this->extractor->extract($squadreRestanti, $numeroEstrazione, $cicliCompletati);

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
        $resetState = $this->extractor->resetState();

        Session::put('ultimaSquadraEstratta', $resetState['ultimaSquadraEstratta']);
        Session::put('squadreRestanti', $resetState['squadreRestanti']);
        Session::put('numeroEstrazione', $resetState['numeroEstrazione']);
        Session::put('cicliCompletati', $resetState['cicliCompletati']);

        return response()->json([
            'squadreRestanti' => $resetState['squadreRestanti'],
        ]);
    }
}
