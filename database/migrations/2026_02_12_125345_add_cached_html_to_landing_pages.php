<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ds_landing_pages', function (Blueprint $table) {
            $table->longText('cached_html')->nullable()->after('builder_content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ds_landing_pages', function (Blueprint $table) {
            $table->dropColumn('cached_html');
        });
    }
};
