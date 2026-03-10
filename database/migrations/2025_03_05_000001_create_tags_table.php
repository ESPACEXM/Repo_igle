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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['mood', 'theme', 'moment', 'tempo'])->default('theme');
            $table->string('color')->default('blue');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('type');
        });

        // Pivot table for song_tag relationship
        Schema::create('song_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['song_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_tag');
        Schema::dropIfExists('tags');
    }
};
