<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtractionDraw extends Model
{
    protected $fillable = [
        'extraction_config_id',
        'team_name',
        'draw_number',
        'completed_cycles',
    ];

    protected $casts = [
        'draw_number' => 'integer',
        'completed_cycles' => 'integer',
    ];

    public function config(): BelongsTo
    {
        return $this->belongsTo(ExtractionConfig::class, 'extraction_config_id');
    }
}
