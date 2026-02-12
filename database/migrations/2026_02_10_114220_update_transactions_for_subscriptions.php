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
        Schema::table('ds_balance_transactions', function (Blueprint $table) {
            $table->string('gateway_slug')->nullable()->after('type');
            $table->string('payment_id')->nullable()->after('gateway_slug');
            $table->string('receipt_path')->nullable()->after('payment_id');
            $table->string('status')->default('completed')->after('receipt_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ds_balance_transactions', function (Blueprint $table) {
            $table->dropColumn(['gateway_slug', 'payment_id', 'receipt_path', 'status']);
        });
    }
};
