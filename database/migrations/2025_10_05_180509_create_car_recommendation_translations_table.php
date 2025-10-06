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
        Schema::create('car_recommendation_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_recommendation_id')->constrained('car_recommendations')->onDelete('cascade');
            $table->string('locale', 5); // ru, en, uk, de, fr, etc.
            $table->text('recommendation');
            $table->timestamps();
            
            // Составной индекс для быстрого поиска по рекомендации и языку
            $table->unique(['car_recommendation_id', 'locale'], 'car_rec_trans_unique');
            
            // Индекс для поиска по языку
            $table->index('locale', 'car_rec_trans_locale_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_recommendation_translations');
    }
};