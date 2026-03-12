<?php

namespace App\Actions\TeamDraw;

use App\Models\ExtractionConfig;
use Illuminate\Support\Facades\Session;

class ClearConfigurationAction
{
    private const CONFIG_TOKEN_KEY = 'extractionConfigToken';

    public function execute(?ExtractionConfig $config): void
    {
        if ($config) {
            $config->delete();
        }

        Session::forget(self::CONFIG_TOKEN_KEY);
    }
}
