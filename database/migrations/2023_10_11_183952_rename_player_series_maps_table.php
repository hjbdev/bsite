<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('player_series_maps', 'player_series_map');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('player_series_map', 'player_series_maps');
    }
};
