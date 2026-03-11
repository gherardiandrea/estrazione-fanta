<?php

namespace Tests\Feature;

use Tests\TestCase;

class SquadraControllerTest extends TestCase
{
    public function test_homepage_returns_ok(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_estrai_returns_expected_payload(): void
    {
        $response = $this->post('/estrai');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'squadra',
                'numeroEstrazione',
                'cicliCompletati',
                'squadreRestanti',
            ]);

        $data = $response->json();

        $this->assertEquals(1, $data['numeroEstrazione']);
        $this->assertEquals(0, $data['cicliCompletati']);
        $this->assertCount(count(config('squadre.list')) - 1, $data['squadreRestanti']);
    }

    public function test_no_duplicate_extractions_in_single_cycle(): void
    {
        $initialList = config('squadre.list');
        $extracted = [];

        for ($i = 0; $i < count($initialList); $i++) {
            $response = $this->post('/estrai')->assertOk();
            $extracted[] = $response->json('squadra');
        }

        $this->assertCount(count($initialList), array_unique($extracted));

        $nextCycleResponse = $this->post('/estrai')->assertOk();

        $this->assertEquals(1, $nextCycleResponse->json('numeroEstrazione'));
        $this->assertEquals(1, $nextCycleResponse->json('cicliCompletati'));
    }

    public function test_reset_restores_initial_state(): void
    {
        $this->post('/estrai')->assertOk();

        $response = $this->post('/reset');

        $response
            ->assertOk()
            ->assertJson([
                'squadreRestanti' => config('squadre.list'),
            ]);

        $this->assertEquals('Nessuna squadra estratta', session('ultimaSquadraEstratta'));
        $this->assertEquals(0, session('numeroEstrazione'));
        $this->assertEquals(0, session('cicliCompletati'));
    }
}
