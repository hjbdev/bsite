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
        Schema::table('series', function (Blueprint $table) {
            $table->foreignId('team_b_id')->nullable()->change();
            $table->json('team_b_data')->nullable();
            $table->string('team_b_name')->nullable();
            $table->string('source_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('series', function (Blueprint $table) {
            $table->foreignId('team_b_id')->nullable(false)->change();
            $table->dropColumn('team_b_data');
            $table->dropColumn('team_b_name');
            $table->dropColumn('source_id');
        });
    }
};
