<?php

use App\Enums\SeriesMapStatus;
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
        Schema::create('series_maps', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('team_a_score')->default(0);
            $table->smallInteger('team_b_score')->default(0);
            $table->foreignId('series_id');
            $table->foreignId('map_id')->index();
            $table->dateTime('start_date')->nullable();
            $table->string('status')->index()->default(SeriesMapStatus::UPCOMING->value);
            $table->index(['map_id', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series_maps');
    }
};
