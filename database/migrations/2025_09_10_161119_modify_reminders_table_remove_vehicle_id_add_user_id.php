<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверяем, существует ли колонка user_id
        if (!Schema::hasColumn('reminders', 'user_id')) {
            Schema::table('reminders', function (Blueprint $table) {
                // Добавляем user_id как nullable сначала
                $table->foreignId('user_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            });

            // Устанавливаем user_id = 1 для всех существующих напоминаний
            DB::table('reminders')->update(['user_id' => 1]);

            Schema::table('reminders', function (Blueprint $table) {
                // Делаем user_id NOT NULL
                $table->foreignId('user_id')->nullable(false)->change();
            });
        }

        // Проверяем, существует ли колонка vehicle_id, и удаляем её
        if (Schema::hasColumn('reminders', 'vehicle_id')) {
            Schema::table('reminders', function (Blueprint $table) {
                // Удаляем внешний ключ vehicle_id
                $table->dropForeign(['vehicle_id']);
                $table->dropColumn('vehicle_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            // Удаляем user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            // Возвращаем vehicle_id
            $table->foreignId('vehicle_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};
