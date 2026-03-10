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
        Schema::table('songs', function (Blueprint $table) {
            // Only add columns if they don't exist (for compatibility with existing databases)
            if (!Schema::hasColumn('songs', 'lyrics')) {
                $table->text('lyrics')->nullable()->after('spotify_url');
            }
            if (!Schema::hasColumn('songs', 'chords')) {
                $table->text('chords')->nullable()->after('lyrics');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('songs', 'lyrics')) {
                $columns[] = 'lyrics';
            }
            if (Schema::hasColumn('songs', 'chords')) {
                $columns[] = 'chords';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
