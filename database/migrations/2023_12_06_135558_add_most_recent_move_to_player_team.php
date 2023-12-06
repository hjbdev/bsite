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
        Schema::table('player_team', function (Blueprint $table) {
            $table->date('most_recent_move')->virtualAs('COALESCE(GREATEST(start_date, end_date), IFNULL(start_date, end_date), IFNULL(end_date, start_date))')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_team', function (Blueprint $table) {
            $table->dropColumn('most_recent_move');
        });
    }
};
