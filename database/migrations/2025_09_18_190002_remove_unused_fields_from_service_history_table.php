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
        Schema::table('expenses_history', function (Blueprint $table) {
            $table->dropColumn(['title', 'mileage', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses_history', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->integer('mileage')->nullable();
            $table->string('type')->default('maintenance');
        });
    }
};
