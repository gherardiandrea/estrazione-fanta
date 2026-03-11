<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extraction_configs', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->json('teams');
            $table->json('remaining_teams');
            $table->string('last_team')->nullable();
            $table->unsignedInteger('draw_number')->default(0);
            $table->unsignedInteger('completed_cycles')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extraction_configs');
    }
};
