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
        if (!Schema::hasColumn('event_user', 'decline_reason')) {
            Schema::table('event_user', function (Blueprint $table) {
                $table->text('decline_reason')->nullable()->after('status');
            });
            echo "Added decline_reason column to event_user table\n";
        } else {
            echo "decline_reason column already exists in event_user table\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('event_user', 'decline_reason')) {
            Schema::table('event_user', function (Blueprint $table) {
                $table->dropColumn('decline_reason');
            });
            echo "Removed decline_reason column from event_user table\n";
        } else {
            echo "decline_reason column does not exist in event_user table\n";
        }
    }
};
