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
        Schema::table('reminders', function (Blueprint $table) {
            $table->dropColumn(['last_service_date', 'last_service_mileage', 'next_service_mileage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->date('last_service_date')->nullable();
            $table->integer('last_service_mileage')->nullable();
            $table->integer('next_service_mileage')->nullable();
        });
    }
};
