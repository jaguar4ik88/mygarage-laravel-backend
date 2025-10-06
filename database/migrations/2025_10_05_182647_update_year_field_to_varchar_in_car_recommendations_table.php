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
        Schema::table('car_recommendations', function (Blueprint $table) {
            // Изменяем тип поля year с int на varchar для поддержки периодов
            $table->string('year')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_recommendations', function (Blueprint $table) {
            // Возвращаем тип поля year обратно к int
            $table->integer('year')->change();
        });
    }
};