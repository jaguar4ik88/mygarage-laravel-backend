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
        Schema::create('reminder_types', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // oil, filters, tires, etc.
            $table->string('icon'); // oil-barrel, filter-alt, etc.
            $table->string('color', 7); // #FFA500, #00FF00, etc.
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_types');
    }
};
