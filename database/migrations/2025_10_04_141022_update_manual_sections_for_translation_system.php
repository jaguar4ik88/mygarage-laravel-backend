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
        Schema::table('manual_sections', function (Blueprint $table) {
            // Удаляем старые поля если они существуют
            if (Schema::hasColumn('manual_sections', 'title')) {
                $table->dropColumn('title');
            }
            
            // Добавляем новые поля только если они не существуют
            if (!Schema::hasColumn('manual_sections', 'title_translation_id')) {
                $table->foreignId('title_translation_id')->nullable()->constrained('translation_groups')->nullOnDelete()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manual_sections', function (Blueprint $table) {
            // Удаляем новое поле если оно существует
            if (Schema::hasColumn('manual_sections', 'title_translation_id')) {
                $table->dropForeign(['title_translation_id']);
                $table->dropColumn('title_translation_id');
            }
            
            // Восстанавливаем старое поле
            if (!Schema::hasColumn('manual_sections', 'title')) {
                $table->string('title')->after('id');
            }
        });
    }
};