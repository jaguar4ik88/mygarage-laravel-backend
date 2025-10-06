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
            // Удаляем поле recommendation, так как теперь используем переводы
            if (Schema::hasColumn('car_recommendations', 'recommendation')) {
                $table->dropColumn('recommendation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_recommendations', function (Blueprint $table) {
            // Восстанавливаем поле recommendation
            if (!Schema::hasColumn('car_recommendations', 'recommendation')) {
                $table->text('recommendation')->nullable()->after('mileage_interval');
            }
        });
    }
};