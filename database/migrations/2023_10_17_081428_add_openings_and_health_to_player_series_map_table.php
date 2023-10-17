<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('player_series_map', function (Blueprint $table) {
            $table->integer('opening_kills')->default(0);
            $table->integer('opening_deaths')->default(0);
            $table->integer('health')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_series_map', function (Blueprint $table) {
            $table->dropColumn('opening_kills');
            $table->dropColumn('opening_deaths');
            $table->dropColumn('health');
        });
    }
};
