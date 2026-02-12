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
        Schema::create('ds_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ds_page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('ds_pages')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('title');
            $table->longText('content');
            $table->timestamps();

            $table->unique(['page_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ds_page_translations');
        Schema::dropIfExists('ds_pages');
    }
};
