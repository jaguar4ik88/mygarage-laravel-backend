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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('platform')->nullable(); // ios, android
            $table->string('transaction_id')->nullable(); // ID транзакции из магазина
            $table->string('original_transaction_id')->nullable(); // оригинальный ID для подписок
            $table->text('receipt_data')->nullable(); // данные чека
            $table->timestamp('cancelled_at')->nullable(); // дата отмены
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['user_id', 'is_active']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
