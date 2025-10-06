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
            // Удаляем поле item, так как теперь используем связь с manual_sections
            if (Schema::hasColumn('car_recommendations', 'item')) {
                $table->dropColumn('item');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_recommendations', function (Blueprint $table) {
            // Восстанавливаем поле item
            if (!Schema::hasColumn('car_recommendations', 'item')) {
                $table->string('item')->after('mileage_interval');
            }
        });
    }
};