<?php

namespace App\Services;

class SquadraExtractorService
{
    public function initialState(array $squadre): array
    {
        return [
            'ultimaSquadraEstratta' => 'Nessuna squadra estratta',
            'numeroEstrazione' => 0,
            'cicliCompletati' => 0,
            'squadreRestanti' => $squadre,
        ];
    }

    public function extract(array $squadreRestanti, int $numeroEstrazione, int $cicliCompletati, array $squadreBase): array
    {
        if (empty($squadreRestanti)) {
            $squadreRestanti = $squadreBase;
            $cicliCompletati++;
            $numeroEstrazione = 0;
        }

        $chiaveEstratta = array_rand($squadreRestanti);
        $squadraEstratta = $squadreRestanti[$chiaveEstratta];

        unset($squadreRestanti[$chiaveEstratta]);

        $numeroEstrazione++;

        return [
            'squadra' => $squadraEstratta,
            'numeroEstrazione' => $numeroEstrazione,
            'cicliCompletati' => $cicliCompletati,
            'squadreRestanti' => array_values($squadreRestanti),
        ];
    }

    public function resetState(array $squadre): array
    {
        return [
            'ultimaSquadraEstratta' => 'Nessuna squadra estratta',
            'numeroEstrazione' => 0,
            'cicliCompletati' => 0,
            'squadreRestanti' => $squadre,
        ];
    }
}
