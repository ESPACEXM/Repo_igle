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
        Schema::table('events', function (Blueprint $table) {
            // Quitar la restricción de foreign key existente
            $table->dropForeign(['created_by']);
            
            // Volver a agregar la foreign key con la opción nullOnDelete()
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
                
            // Hacer la columna nullable
            $table->unsignedBigInteger('created_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Quitar la restricción de foreign key con nullOnDelete()
            $table->dropForeign(['created_by']);
            
            // Volver a agregar la foreign key original
            $table->foreign('created_by')
                ->references('id')
                ->on('users');
                
            // Hacer la columna no nullable
            $table->unsignedBigInteger('created_by')->nullable(false)->change();
        });
    }
};
