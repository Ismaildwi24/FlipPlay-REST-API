<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('type');
            $table->json('genres');
            $table->decimal('rating', 3, 1)->default(0.0);
            $table->integer('release_year');
            $table->string('aura');
            $table->text('synopsis')->nullable();

            $table->integer('episode_count')->nullable();
            $table->string('studio')->nullable();
            $table->string('status')->nullable()->default('Completed');

            $table->integer('duration_minutes')->nullable();
            $table->string('director')->nullable();
            $table->string('quality')->nullable()->default('4K Ultra HD');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
