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
        Schema::table('event_user', function (Blueprint $table) {
            if (!Schema::hasColumn('event_user', 'declined_at')) {
                $table->timestamp('declined_at')->nullable()->after('responded_at');
            }
        });
        
        // También agregar a attendances si no existe
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'declined_at')) {
                $table->timestamp('declined_at')->nullable()->after('responded_at');
            }
            if (!Schema::hasColumn('attendances', 'decline_reason')) {
                $table->text('decline_reason')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_user', function (Blueprint $table) {
            if (Schema::hasColumn('event_user', 'declined_at')) {
                $table->dropColumn('declined_at');
            }
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'declined_at')) {
                $table->dropColumn('declined_at');
            }
            if (Schema::hasColumn('attendances', 'decline_reason')) {
                $table->dropColumn('decline_reason');
            }
        });
    }
};
