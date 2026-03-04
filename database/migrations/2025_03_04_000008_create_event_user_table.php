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
        Schema::create('event_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('instrument_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['confirmed', 'pending', 'declined'])->default('pending');
            $table->boolean('notification_sent')->default(false);
            $table->timestamps();

            $table->index('event_id');
            $table->index('user_id');
            $table->index('instrument_id');
            $table->index('status');
            $table->unique(['event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_user');
    }
};