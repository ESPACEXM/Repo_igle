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
            // Si requiere confirmación del usuario (true) o solo es informativo (false)
            if (!Schema::hasColumn('event_user', 'requires_confirmation')) {
                $table->boolean('requires_confirmation')->default(true)->after('status');
            }
            
            // Fecha cuando el usuario respondió
            if (!Schema::hasColumn('event_user', 'responded_at')) {
                $table->timestamp('responded_at')->nullable()->after('notes');
            }
            
            // Tipo de notificación: 'confirmation' (requiere respuesta) o 'reminder' (solo informativo)
            if (!Schema::hasColumn('event_user', 'notification_type')) {
                $table->enum('notification_type', ['confirmation', 'reminder'])->default('confirmation')->after('requires_confirmation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_user', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('event_user', 'requires_confirmation')) {
                $columns[] = 'requires_confirmation';
            }
            if (Schema::hasColumn('event_user', 'responded_at')) {
                $columns[] = 'responded_at';
            }
            if (Schema::hasColumn('event_user', 'notification_type')) {
                $columns[] = 'notification_type';
            }
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
