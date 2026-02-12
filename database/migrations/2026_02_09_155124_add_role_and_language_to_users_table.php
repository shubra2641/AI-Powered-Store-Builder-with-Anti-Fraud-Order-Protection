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
            $table->foreignId('role_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->foreignId('language_id')->nullable()->after('role_id')->constrained()->onDelete('set null');
            $table->boolean('is_active')->default(false)->after('password');
            $table->string('activation_token')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['language_id']);
            $table->dropColumn(['role_id', 'language_id', 'is_active', 'activation_token']);
        });
    }
};
