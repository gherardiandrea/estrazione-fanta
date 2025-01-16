<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SquadraController extends Controller
{
    private $squadre = [
        'Beghe',
        'Fusellami',
        'Bunga bunga',
        'Degrado sul divano',
        'Real vergogna',
        'Maiafic',
        'Dinamo',
        'Tim Fucchio',
        'Madenzi',
        'L\'imane'
    ];

    public function index()
    {
        $ultimaSquadraEstratta = Session::get('ultimaSquadraEstratta', 'Nessuna squadra estratta');
        $squadreRestanti = Session::get('squadreRestanti', $this->squadre);
        $numeroEstrazione = Session::get('numeroEstrazione', 0);
        $cicliCompletati = Session::get('cicliCompletati', 0);

        return view('squadre', [
            'squadra' => $ultimaSquadraEstratta,
            'numeroEstrazione' => $numeroEstrazione,
            'cicliCompletati' => $cicliCompletati,
            'squadreRestanti' => $squadreRestanti
        ]);
    }

    public function estrai()
    {
        $squadreRestanti = Session::get('squadreRestanti', $this->squadre);
        $numeroEstrazione = Session::get('numeroEstrazione', 0);
        $cicliCompletati = Session::get('cicliCompletati', 0);

        if (empty($squadreRestanti)) {
            $squadreRestanti = $this->squadre;
            $cicliCompletati++;
            $numeroEstrazione = 0;
        }

        $chiaveEstratta = array_rand($squadreRestanti);
        $squadraEstratta = $squadreRestanti[$chiaveEstratta];

        unset($squadreRestanti[$chiaveEstratta]);

        $numeroEstrazione++;

        Session::put('ultimaSquadraEstratta', $squadraEstratta);
        Session::put('numeroEstrazione', $numeroEstrazione);
        Session::put('cicliCompletati', $cicliCompletati);
        Session::put('squadreRestanti', $squadreRestanti);

        return response()->json([
            'squadra' => $squadraEstratta,
            'numeroEstrazione' => $numeroEstrazione,
            'cicliCompletati' => $cicliCompletati,
            'squadreRestanti' => array_values($squadreRestanti)
        ]);
    }

    public function reset()
    {
        Session::forget('ultimaSquadraEstratta');
        Session::put('squadreRestanti', $this->squadre);
        Session::forget('numeroEstrazione');
        Session::forget('cicliCompletati');

        return response()->json([
            'squadreRestanti' => array_values($this->squadre)
        ]);
    }
}
