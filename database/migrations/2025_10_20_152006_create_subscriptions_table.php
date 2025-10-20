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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // free, pro, premium
            $table->string('display_name'); // Free, PRO, Premium
            $table->integer('price')->default(0); // цена в центах
            $table->integer('duration_days')->default(30); // длительность подписки
            $table->json('features')->nullable(); // JSON с доступными функциями
            $table->integer('max_vehicles')->default(1); // макс. количество авто
            $table->integer('max_reminders')->nullable(); // макс. напоминаний (null = безлимит)
            $table->boolean('is_active')->default(true); // активна ли подписка
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
