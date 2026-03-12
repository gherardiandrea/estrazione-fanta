<?php

namespace Tests\Feature;

use App\Models\ExtractionConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamDrawControllerTest extends TestCase
{
    use RefreshDatabase;

    private function setupDefaultTeams(): void
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
        $this->assertEquals(config('teams.list'), $config->teams);
        $this->assertEquals(config('teams.list'), $config->remaining_teams);
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

    public function test_draw_requires_setup(): void
    {
        $this->post('/draw')
            ->assertStatus(422)
            ->assertJson([
                'message' => "Configura prima l'elenco squadre.",
            ]);
    }

    public function test_draw_returns_expected_payload(): void
    {
        $this->setupDefaultTeams();

        $response = $this->post('/draw');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'team',
                'drawNumber',
                'completedCycles',
                'remainingTeams',
            ]);

        $data = $response->json();

        $this->assertEquals(1, $data['drawNumber']);
        $this->assertEquals(0, $data['completedCycles']);
        $this->assertCount(count(config('teams.list')) - 1, $data['remainingTeams']);
    }

    public function test_no_duplicate_extractions_in_single_cycle(): void
    {
        $this->setupDefaultTeams();

        $initialList = config('teams.list');
        $extracted = [];

        for ($i = 0; $i < count($initialList); $i++) {
            $response = $this->post('/draw')->assertOk();
            $extracted[] = $response->json('team');
        }

        $this->assertCount(count($initialList), array_unique($extracted));

        $nextCycleResponse = $this->post('/draw')->assertOk();

        $this->assertEquals(1, $nextCycleResponse->json('drawNumber'));
        $this->assertEquals(1, $nextCycleResponse->json('completedCycles'));
    }

    public function test_reset_restores_initial_state(): void
    {
        $this->setupDefaultTeams();

        $this->post('/draw')->assertOk();

        $response = $this->post('/reset');

        $response
            ->assertOk()
            ->assertJson([
                'remainingTeams' => config('teams.list'),
            ]);

        $token = session('extractionConfigToken');
        $config = ExtractionConfig::where('token', $token)->first();

        $this->assertNotNull($config);
        $this->assertNull($config->last_team);
        $this->assertEquals(config('teams.list'), $config->remaining_teams);
        $this->assertEquals(0, $config->draw_number);
        $this->assertEquals(0, $config->completed_cycles);
    }

    public function test_setup_custom_parses_trims_and_deduplicates_teams(): void
    {
        $custom = "  Ajax , Milan;\nInter\nMilan  ";

        $this->post('/setup', [
            'mode' => 'custom',
            'custom_teams' => $custom,
        ])->assertRedirect('/');

        $token = session('extractionConfigToken');
        $config = ExtractionConfig::where('token', $token)->first();

        $this->assertNotNull($config);
        $this->assertEquals(['Ajax', 'Milan', 'Inter'], $config->teams);
    }

    public function test_setup_custom_with_less_than_two_teams_fails_validation(): void
    {
        $response = $this->from('/')->post('/setup', [
            'mode' => 'custom',
            'custom_teams' => 'SoloTeam',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('custom_teams');

        $this->assertDatabaseCount('extraction_configs', 0);
    }

    public function test_new_configuration_deletes_config_and_token(): void
    {
        $this->setupDefaultTeams();

        $token = session('extractionConfigToken');
        $this->assertNotEmpty($token);
        $this->assertDatabaseHas('extraction_configs', ['token' => $token]);

        $this->post('/new-configuration')->assertRedirect('/');

        $this->assertDatabaseCount('extraction_configs', 0);
        $this->assertNull(session('extractionConfigToken'));
    }

    public function test_draw_persists_state_across_requests(): void
    {
        $this->setupDefaultTeams();

        $this->post('/draw')->assertOk();
        $this->post('/draw')->assertOk();

        $token = session('extractionConfigToken');
        $config = ExtractionConfig::where('token', $token)->first();

        $this->assertNotNull($config);
        $this->assertEquals(2, $config->draw_number);
        $this->assertCount(count(config('teams.list')) - 2, $config->remaining_teams);
    }

    public function test_reset_restores_custom_team_set(): void
    {
        $custom = "Ajax\nMilan\nInter";

        $this->post('/setup', [
            'mode' => 'custom',
            'custom_teams' => $custom,
        ])->assertRedirect('/');

        $this->post('/draw')->assertOk();

        $response = $this->post('/reset')->assertOk();

        $response->assertJson([
            'remainingTeams' => ['Ajax', 'Milan', 'Inter'],
        ]);
    }
}
