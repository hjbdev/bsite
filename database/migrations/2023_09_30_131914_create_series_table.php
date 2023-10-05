<?php

use App\Enums\SeriesStatus;
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
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable();
            $table->foreignId('event_id')->nullable();
            $table->foreignId('team_a_id');
            $table->foreignId('team_b_id');
            $table->foreignId('current_series_map_id')->nullable();
            $table->smallInteger('team_a_score')->default(0);
            $table->smallInteger('team_b_score')->default(0);
            $table->smallInteger('rounds_played')->default(0);
            $table->text('server_token')->nullable();
            $table->string('type');
            $table->string('status')->default(SeriesStatus::UPCOMING->value);
            $table->dateTime('start_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
