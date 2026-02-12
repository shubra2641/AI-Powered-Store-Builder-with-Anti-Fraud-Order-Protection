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
        Schema::create('ds_landing_page_components', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('blade_template');
            $table->longText('config_schema')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ds_landing_page_components');
    }
};
