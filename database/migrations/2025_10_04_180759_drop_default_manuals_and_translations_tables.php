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
        // Удаляем таблицы в правильном порядке (сначала дочерние, потом родительские)
        Schema::dropIfExists('default_manual_translations');
        Schema::dropIfExists('default_manuals');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Восстанавливаем таблицы (в обратном порядке)
        
        // Создаем таблицу default_manuals
        Schema::create('default_manuals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manual_section_id')->constrained('manual_sections')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('icon')->nullable();
            $table->string('pdf_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Создаем таблицу default_manual_translations
        Schema::create('default_manual_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('default_manual_id')->constrained('default_manuals')->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('title');
            $table->text('content');
            $table->timestamps();
            
            $table->unique(['default_manual_id', 'locale']);
        });
    }
};