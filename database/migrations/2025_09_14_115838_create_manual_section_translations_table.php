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
        Schema::create('manual_section_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manual_section_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5); // en, ru, etc.
            $table->string('title');
            $table->timestamps();
            
            $table->unique(['manual_section_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_section_translations');
    }
};
