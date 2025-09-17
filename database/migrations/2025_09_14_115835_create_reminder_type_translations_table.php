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
        Schema::create('reminder_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reminder_type_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5); // en, ru, etc.
            $table->string('title');
            $table->timestamps();
            
            $table->unique(['reminder_type_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_type_translations');
    }
};
