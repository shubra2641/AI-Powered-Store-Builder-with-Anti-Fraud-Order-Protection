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
        Schema::table('ds_ai_keys', function (Blueprint $table) {
            $table->unsignedBigInteger('tokens_used')->default(0)->after('max_tokens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ds_ai_keys', function (Blueprint $table) {
            $table->dropColumn('tokens_used');
        });
    }
};
