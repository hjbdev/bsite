<?php

use App\Enums\DemoStatus;
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
        Schema::create('demos', function (Blueprint $table) {
            $table->id();
            $table->string('disk')->default('local');
            $table->string('path')->nullable();
            $table->foreignId('series_map_id');
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedBigInteger('compressed_size')->nullable();
            $table->string('status')->default(DemoStatus::NONE->value);
            $table->bigInteger('downloads')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demos');
    }
};
