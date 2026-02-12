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
        Schema::create('ds_ai_keys', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // gemini, chatgpt, groq, claude, perplexity
            $table->string('model');
            $table->string('api_key');
            $table->integer('max_tokens')->default(2000);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ds_ai_keys');
    }
};
