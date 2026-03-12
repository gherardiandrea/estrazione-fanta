<?php

namespace App\Actions\TeamDraw;

use App\Models\ExtractionConfig;

class ClearDrawHistoryAction
{
    public function execute(ExtractionConfig $config): array
    {
        $config->draws()->delete();

        return $config->historyPayload();
    }
}
