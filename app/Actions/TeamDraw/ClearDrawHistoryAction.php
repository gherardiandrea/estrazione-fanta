<?php

namespace App\Actions\TeamDraw;

use App\Models\ExtractionConfig;

class ClearDrawHistoryAction
{
    public function execute(ExtractionConfig $config): array
    {
        $config->draws()->delete();
        $config->update([
            'completed_cycles' => 0,
        ]);

        return array_merge([
            'completedCycles' => 0,
        ], $config->historyPayload());
    }
}
