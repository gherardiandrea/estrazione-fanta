<?php

$defaultTeamsRaw = env('DEFAULT_TEAMS', 'Team Alpha|Team Beta|Team Gamma|Team Delta');
$defaultTeams = array_values(array_filter(array_map('trim', explode('|', $defaultTeamsRaw))));

return [
    // Keep personal team names in .env (DEFAULT_TEAMS) to avoid publishing them.
    'list' => $defaultTeams,
];
