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
        Schema::table('email_templates', function (Blueprint $table) {
            $table->string('name')->default('Template')->after('slug');
            $table->boolean('is_system')->default(false)->after('content');
            $table->text('description')->nullable()->after('is_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn(['name', 'is_system', 'description']);
        });
    }
};
