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
        Schema::create('ds_landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->longText('builder_content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ds_landing_page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('ds_landing_pages')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['landing_page_id', 'language_id'], 'ds_lp_trans_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ds_landing_page_translations');
        Schema::dropIfExists('ds_landing_pages');
    }
};
