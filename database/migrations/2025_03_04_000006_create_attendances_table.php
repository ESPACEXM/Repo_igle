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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rehearsal_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'late', 'justified'])->default('absent');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('rehearsal_id');
            $table->index('event_id');
            $table->index('user_id');
            $table->index('status');

            // Unique constraints to prevent duplicate attendance records
            // SQLite allows multiple NULLs in UNIQUE constraints, which is the desired behavior
            $table->unique(['rehearsal_id', 'user_id']);
            $table->unique(['event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};