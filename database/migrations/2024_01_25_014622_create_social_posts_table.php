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
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->morphs('postable');
            $table->string('type');
            $table->text('content');
            $table->string('platform');
            $table->string('platform_post_id')->nullable();
            $table->string('platform_post_url')->nullable();
            $table->string('link_to_post')->nullable();
            $table->string('generator_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
