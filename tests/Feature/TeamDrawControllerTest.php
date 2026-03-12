<?php

namespace Tests\Feature;

use App\Models\ExtractionConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SquadraControllerTest extends TestCase
{
    use RefreshDatabase;

    private function setupDefaultSquadre(): void
    {
        $this->post('/setup', ['mode' => 'default'])->assertRedirect('/');
    }

    public function test_homepage_returns_ok(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_setup_default_initializes_persisted_state(): void
    {
        $this->post('/setup', ['mode' => 'default'])->assertRedirect('/');

        $token = session('extractionConfigToken');
        $this->assertNotEmpty($token);

        $config = ExtractionConfig::where('token', $token)->first();

        $this->assertNotNull($config);
        $this->assertEquals(config('squadre.list'), $config->teams);
        $this->assertEquals(config('squadre.list'), $config->remaining_teams);
        $this->assertEquals(0, $config->draw_number);
        $this->assertEquals(0, $config->completed_cycles);
    }

    public function test_setup_custom_initializes_persisted_state(): void
    {
        $custom = "Ajax\nMilan\nInter";

        $this->post('/setup', [
            'mode' => 'custom',
            'custom_teams' => $custom,
        ])->assertRedirect('/');

        $token = session('extractionConfigToken');
        $config = ExtractionConfig::where('token', $token)->first();

        $this->assertNotNull($config);
        $this->assertEquals(['Ajax', 'Milan', 'Inter'], $config->teams);
        $this->assertEquals(['Ajax', 'Milan', 'Inter'], $config->remaining_teams);
    }

    public function test_estrai_requires_setup(): void
    {
        $this->post('/estrai')
            ->assertStatus(422)
            ->assertJson([
                'message' => "Configura prima l'elenco squadre.",
            ]);
    }

    public function test_estrai_returns_expected_payload(): void
    {
        $this->setupDefaultSquadre();

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
        $this->setupDefaultSquadre();

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
        $this->setupDefaultSquadre();

        $this->post('/estrai')->assertOk();

        $response = $this->post('/reset');

        $response
            ->assertOk()
            ->assertJson([
                'squadreRestanti' => config('squadre.list'),
            ]);

        $token = session('extractionConfigToken');
        $config = ExtractionConfig::where('token', $token)->first();

        $this->assertNotNull($config);
        $this->assertNull($config->last_team);
        $this->assertEquals(config('squadre.list'), $config->remaining_teams);
        $this->assertEquals(0, $config->draw_number);
        $this->assertEquals(0, $config->completed_cycles);
    }
}
