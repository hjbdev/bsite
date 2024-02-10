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
            $table->string('crosshair_share_code')->nullable();
            $table->integer('inspect_weapon_count')->nullable();
            $table->float('kill_death_ratio', 8, 2)->nullable();
            $table->integer('bomb_defused_count')->nullable();
            $table->integer('bomb_planted_count')->nullable();
            $table->integer('armor_damage')->nullable();
            $table->integer('utility_damage')->nullable();
            $table->integer('headshot_count')->nullable();
            $table->integer('headshot_percent')->nullable();
            $table->integer('one_vs_one_count')->nullable();
            $table->integer('one_vs_one_won_count')->nullable();
            $table->integer('one_vs_one_lost_count')->nullable();
            $table->integer('one_vs_two_count')->nullable();
            $table->integer('one_vs_two_won_count')->nullable();
            $table->integer('one_vs_two_lost_count')->nullable();
            $table->integer('one_vs_three_count')->nullable();
            $table->integer('one_vs_three_won_count')->nullable();
            $table->integer('one_vs_three_lost_count')->nullable();
            $table->integer('one_vs_four_count')->nullable();
            $table->integer('one_vs_four_won_count')->nullable();
            $table->integer('one_vs_four_lost_count')->nullable();
            $table->integer('one_vs_five_count')->nullable();
            $table->integer('one_vs_five_won_count')->nullable();
            $table->integer('one_vs_five_lost_count')->nullable();
            $table->float('average_kill_per_round', 8, 2)->nullable();
            $table->float('average_death_per_round', 8, 2)->nullable();
            $table->float('average_damage_per_round', 8, 2)->nullable();
            $table->float('utility_damage_per_round', 8, 2)->nullable();
            $table->integer('first_kill_count')->nullable();
            $table->integer('first_death_count')->nullable();
            $table->integer('first_trade_death_count')->nullable();
            $table->integer('trade_death_count')->nullable();
            $table->integer('trade_kill_count')->nullable();
            $table->integer('first_trade_kill_count')->nullable();
            $table->integer('one_kill_count')->nullable();
            $table->integer('two_kill_count')->nullable();
            $table->integer('three_kill_count')->nullable();
            $table->integer('four_kill_count')->nullable();
            $table->integer('five_kill_count')->nullable();
            $table->float('hltv_rating', 8, 2)->nullable();
            $table->float('hltv_rating2', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_series_map', function (Blueprint $table) {
            $table->dropColumn('crosshair_share_code');
            $table->dropColumn('inspect_weapon_count');
            $table->dropColumn('kill_death_ratio');
            $table->dropColumn('bomb_defused_count');
            $table->dropColumn('bomb_planted_count');
            $table->dropColumn('armor_damage');
            $table->dropColumn('utility_damage');
            $table->dropColumn('headshot_count');
            $table->dropColumn('headshot_percent');
            $table->dropColumn('one_vs_one_count');
            $table->dropColumn('one_vs_one_won_count');
            $table->dropColumn('one_vs_one_lost_count');
            $table->dropColumn('one_vs_two_count');
            $table->dropColumn('one_vs_two_won_count');
            $table->dropColumn('one_vs_two_lost_count');
            $table->dropColumn('one_vs_three_count');
            $table->dropColumn('one_vs_three_won_count');
            $table->dropColumn('one_vs_three_lost_count');
            $table->dropColumn('one_vs_four_count');
            $table->dropColumn('one_vs_four_won_count');
            $table->dropColumn('one_vs_four_lost_count');
            $table->dropColumn('one_vs_five_count');
            $table->dropColumn('one_vs_five_won_count');
            $table->dropColumn('one_vs_five_lost_count');
            $table->dropColumn('average_kill_per_round');
            $table->dropColumn('average_death_per_round');
            $table->dropColumn('average_damage_per_round');
            $table->dropColumn('utility_damage_per_round');
            $table->dropColumn('first_kill_count');
            $table->dropColumn('first_death_count');
            $table->dropColumn('first_trade_death_count');
            $table->dropColumn('trade_death_count');
            $table->dropColumn('trade_kill_count');
            $table->dropColumn('first_trade_kill_count');
            $table->dropColumn('one_kill_count');
            $table->dropColumn('two_kill_count');
            $table->dropColumn('three_kill_count');
            $table->dropColumn('four_kill_count');
            $table->dropColumn('five_kill_count');
            $table->dropColumn('hltv_rating');
            $table->dropColumn('hltv_rating2');
        });
    }
};
