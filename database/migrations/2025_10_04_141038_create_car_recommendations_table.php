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
        Schema::create('car_recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('maker'); // Марка (например, Toyota)
            $table->string('model'); // Модель (например, Corolla)
            $table->integer('year'); // Год выпуска
            $table->integer('mileage_interval'); // Пробег (например, 10000 км)
            $table->string('item'); // Что менять (масло, фильтр, свечи)
            $table->text('recommendation'); // Рекомендация (например, «Менять масло каждые 10 000 км, использовать 5W-30»)
            $table->foreignId('manual_section_id')->nullable()->constrained()->nullOnDelete(); // Связь с manual_sections
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index(['maker', 'model', 'year']);
            $table->index(['mileage_interval']);
            $table->index(['item']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_recommendations');
    }
};