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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->enum('role', ['leader', 'member'])->default('member')->after('phone');
            $table->boolean('is_active')->default(true)->after('role');
            $table->text('notes')->nullable()->after('is_active');

            $table->index('role');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['phone']);
            $table->dropColumn(['phone', 'role', 'is_active', 'notes']);
        });
    }
};

