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
        // Rename name to location (if name exists)
        if (Schema::hasColumn('rehearsals', 'name')) {
            Schema::table('rehearsals', function (Blueprint $table) {
                $table->renameColumn('name', 'location');
            });
        }
        
        // Add new columns if they don't exist
        Schema::table('rehearsals', function (Blueprint $table) {
            if (!Schema::hasColumn('rehearsals', 'notes')) {
                $table->text('notes')->nullable()->after('location');
            }
            if (!Schema::hasColumn('rehearsals', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            }
        });

        // Add index separately with explicit name to avoid conflicts
        if (Schema::hasColumn('rehearsals', 'created_by')) {
            $indexes = Schema::getIndexes('rehearsals');
            $hasCreatedByIndex = false;
            foreach ($indexes as $index) {
                if (in_array('created_by', $index['columns'] ?? [])) {
                    $hasCreatedByIndex = true;
                    break;
                }
            }
            if (!$hasCreatedByIndex) {
                Schema::table('rehearsals', function (Blueprint $table) {
                    $table->index('created_by', 'rehearsals_created_by_index');
                });
            }
        }
        
        // Make location nullable
        Schema::table('rehearsals', function (Blueprint $table) {
            $table->string('location')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rehearsals', function (Blueprint $table) {
            if (Schema::hasColumn('rehearsals', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropIndex(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('rehearsals', 'notes')) {
                $table->dropColumn('notes');
            }
        });
        
        if (Schema::hasColumn('rehearsals', 'location')) {
            Schema::table('rehearsals', function (Blueprint $table) {
                $table->string('location')->nullable(false)->change();
                $table->renameColumn('location', 'name');
            });
        }
    }
};
