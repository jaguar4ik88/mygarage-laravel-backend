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
        Schema::create('car_tyres', function (Blueprint $table) {
            $table->id();
            $table->string('brand'); // Марка шин
            $table->string('model'); // Модель автомобиля
            $table->integer('year'); // Год выпуска
            $table->string('dimension'); // Размер передних шин (например, 205/55 R16)
            $table->text('notes')->nullable(); // Дополнительно (например, «Можно ставить RunFlat»)
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index(['brand', 'model', 'year']);
            $table->index(['dimension']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_tyres');
    }
};