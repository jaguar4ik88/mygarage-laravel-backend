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
        Schema::table('users', function (Blueprint $table) {
            // Добавляем только новые поля (plan_type и subscription_expires_at уже есть!)
            $table->string('platform')->nullable()->after('subscription_expires_at'); // ios, android
            $table->string('transaction_id')->nullable()->after('platform'); // последний ID транзакции
            $table->boolean('reminder_expenses_enabled')->default(false)->after('transaction_id'); // напоминания о тратах
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['platform', 'transaction_id', 'reminder_expenses_enabled']);
        });
    }
};
