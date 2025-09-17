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
        Schema::create('faq_question_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_question_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5); // en, ru, etc.
            $table->string('question');
            $table->text('answer');
            $table->timestamps();
            
            $table->unique(['faq_question_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_question_translations');
    }
};
