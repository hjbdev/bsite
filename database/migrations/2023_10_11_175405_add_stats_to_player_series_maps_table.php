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
        Schema::table('player_series_maps', function (Blueprint $table) {
            $table->integer('kills')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('deaths')->default(0);
            $table->integer('damage')->default(0);
            $table->integer('traded')->default(0);
            $table->float('kast')->default(0);
            $table->float('rating')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_series_maps', function (Blueprint $table) {
            $table->dropColumn('kills');
            $table->dropColumn('assists');
            $table->dropColumn('deaths');
            $table->dropColumn('damage');
            $table->dropColumn('traded');
            $table->dropColumn('kast');
            $table->dropColumn('rating');
        });
    }
};
