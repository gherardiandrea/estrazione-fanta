<?php

namespace Tests\Unit;

use App\Services\TeamDrawService;
use PHPUnit\Framework\TestCase;

class TeamDrawServiceTest extends TestCase
{
    private TeamDrawService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new TeamDrawService();
    }

    public function test_initial_state_contains_expected_defaults(): void
    {
        $teams = ['A', 'B', 'C'];

        $state = $this->service->initialState($teams);

        $this->assertSame('Nessuna squadra estratta', $state['lastDrawnTeam']);
        $this->assertSame(0, $state['drawNumber']);
        $this->assertSame(0, $state['completedCycles']);
        $this->assertSame($teams, $state['remainingTeams']);
    }

    public function test_reset_state_restores_initial_values(): void
    {
        $teams = ['X', 'Y'];

        $state = $this->service->resetState($teams);

        $this->assertSame('Nessuna squadra estratta', $state['lastDrawnTeam']);
        $this->assertSame(0, $state['drawNumber']);
        $this->assertSame(0, $state['completedCycles']);
        $this->assertSame($teams, $state['remainingTeams']);
    }

    public function test_extract_removes_one_team_and_increments_draw_number(): void
    {
        $baseTeams = ['Alpha', 'Beta', 'Gamma'];

        $result = $this->service->extract($baseTeams, 0, 0, $baseTeams);

        $this->assertContains($result['team'], $baseTeams);
        $this->assertSame(1, $result['drawNumber']);
        $this->assertSame(0, $result['completedCycles']);
        $this->assertCount(2, $result['remainingTeams']);
        $this->assertNotContains($result['team'], $result['remainingTeams']);
    }

    public function test_extract_increments_completed_cycles_when_last_team_is_drawn(): void
    {
        $baseTeams = ['OnlyTeam'];

        $result = $this->service->extract(['OnlyTeam'], 0, 2, $baseTeams);

        $this->assertSame('OnlyTeam', $result['team']);
        $this->assertSame(1, $result['drawNumber']);
        $this->assertSame(3, $result['completedCycles']);
        $this->assertSame([], $result['remainingTeams']);
    }

    public function test_extract_starts_new_cycle_when_remaining_is_empty(): void
    {
        $baseTeams = ['A', 'B', 'C'];

        $result = $this->service->extract([], 3, 1, $baseTeams);

        $this->assertContains($result['team'], $baseTeams);
        $this->assertSame(1, $result['drawNumber']);
        $this->assertSame(1, $result['completedCycles']);
        $this->assertCount(2, $result['remainingTeams']);
    }
}
