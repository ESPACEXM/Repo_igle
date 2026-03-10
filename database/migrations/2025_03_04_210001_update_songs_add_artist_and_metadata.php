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
        // Rename columns if they exist
        if (Schema::hasColumn('songs', 'author')) {
            Schema::table('songs', function (Blueprint $table) {
                $table->renameColumn('author', 'artist');
            });
        }
        if (Schema::hasColumn('songs', 'bpm')) {
            Schema::table('songs', function (Blueprint $table) {
                $table->renameColumn('bpm', 'tempo');
            });
        }
        if (Schema::hasColumn('songs', 'link')) {
            Schema::table('songs', function (Blueprint $table) {
                $table->renameColumn('link', 'youtube_url');
            });
        }
        
        // Add new columns if they don't exist
        Schema::table('songs', function (Blueprint $table) {
            if (!Schema::hasColumn('songs', 'duration')) {
                $table->integer('duration')->nullable()->after('tempo');
            }
            if (!Schema::hasColumn('songs', 'lyrics_url')) {
                $table->string('lyrics_url')->nullable()->after('duration');
            }
            if (!Schema::hasColumn('songs', 'chords_url')) {
                $table->string('chords_url')->nullable()->after('lyrics_url');
            }
            if (!Schema::hasColumn('songs', 'spotify_url')) {
                $table->string('spotify_url')->nullable()->after('chords_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            // Drop new columns if they exist
            if (Schema::hasColumn('songs', 'spotify_url')) {
                $table->dropColumn('spotify_url');
            }
            if (Schema::hasColumn('songs', 'chords_url')) {
                $table->dropColumn('chords_url');
            }
            if (Schema::hasColumn('songs', 'lyrics_url')) {
                $table->dropColumn('lyrics_url');
            }
            if (Schema::hasColumn('songs', 'duration')) {
                $table->dropColumn('duration');
            }
        });
        
        // Rename back if columns exist
        if (Schema::hasColumn('songs', 'youtube_url')) {
            Schema::table('songs', function (Blueprint $table) {
                $table->renameColumn('youtube_url', 'link');
            });
        }
        if (Schema::hasColumn('songs', 'tempo')) {
            Schema::table('songs', function (Blueprint $table) {
                $table->renameColumn('tempo', 'bpm');
            });
        }
        if (Schema::hasColumn('songs', 'artist')) {
            Schema::table('songs', function (Blueprint $table) {
                $table->renameColumn('artist', 'author');
            });
        }
    }
};
