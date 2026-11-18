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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('type'); // 'Anime' atau 'Movie'
            $table->json('genres'); // ['Action', 'Fantasy']
            $table->decimal('rating', 3, 1)->default(0.0);
            $table->integer('release_year');
            $table->string('aura'); // 'Binge-Mode', 'High-Energy', 'Melancholy', 'Mind-Bending'
            $table->text('synopsis')->nullable();

            // Atribut spesifik Anime (Modul 3)
            $table->integer('episode_count')->nullable();
            $table->string('studio')->nullable();
            $table->string('status')->nullable()->default('Completed');

            // Atribut spesifik Movie (Modul 3)
            $table->integer('duration_minutes')->nullable();
            $table->string('director')->nullable();
            $table->string('quality')->nullable()->default('4K Ultra HD');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
