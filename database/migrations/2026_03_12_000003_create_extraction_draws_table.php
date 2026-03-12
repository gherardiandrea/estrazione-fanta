<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extraction_draws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extraction_config_id')
                ->constrained('extraction_configs')
                ->cascadeOnDelete();
            $table->string('team_name');
            $table->unsignedInteger('draw_number');
            $table->unsignedInteger('completed_cycles');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extraction_draws');
    }
};
