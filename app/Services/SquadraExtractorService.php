<?php

namespace App\Services;

class SquadraExtractorService
{
    private function squadre(): array
    {
        return config('squadre.list', []);
    }

    public function initialState(): array
    {
        return [
            'ultimaSquadraEstratta' => 'Nessuna squadra estratta',
            'numeroEstrazione' => 0,
            'cicliCompletati' => 0,
            'squadreRestanti' => $this->squadre(),
        ];
    }

    public function extract(array $squadreRestanti, int $numeroEstrazione, int $cicliCompletati): array
    {
        if (empty($squadreRestanti)) {
            $squadreRestanti = $this->squadre();
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

    public function resetState(): array
    {
        return [
            'ultimaSquadraEstratta' => 'Nessuna squadra estratta',
            'numeroEstrazione' => 0,
            'cicliCompletati' => 0,
            'squadreRestanti' => $this->squadre(),
        ];
    }
}
