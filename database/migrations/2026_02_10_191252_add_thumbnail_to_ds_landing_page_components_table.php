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
        Schema::table('ds_landing_page_components', function (Blueprint $table) {
            $table->string('thumbnail')->after('blade_template')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ds_landing_page_components', function (Blueprint $table) {
            if (Schema::hasColumn('ds_landing_page_components', 'thumbnail')) {
                $table->dropColumn('thumbnail');
            }
        });
    }
};
