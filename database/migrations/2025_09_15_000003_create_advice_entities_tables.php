<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advice_sections', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('title_translation_id')->nullable()->constrained('translation_groups')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('advice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advice_section_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('title_translation_id')->nullable()->constrained('translation_groups')->nullOnDelete();
            $table->foreignId('content_translation_id')->nullable()->constrained('translation_groups')->nullOnDelete();
            $table->string('icon')->nullable();
            $table->string('pdf_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advice_items');
        Schema::dropIfExists('advice_sections');
    }
};


