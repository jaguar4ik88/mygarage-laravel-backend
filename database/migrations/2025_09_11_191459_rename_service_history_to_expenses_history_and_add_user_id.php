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
        // Add user_id column to service_history table (nullable first, then we'll populate it)
        Schema::table('service_history', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        // Populate user_id for existing records (assign to first user or create a default)
        $firstUser = \App\Models\User::first();
        if ($firstUser) {
            \DB::table('service_history')->update(['user_id' => $firstUser->id]);
        }

        // Make user_id NOT NULL after populating
        Schema::table('service_history', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });

        // Rename table from service_history to expenses_history
        Schema::rename('service_history', 'expenses_history');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename table back from expenses_history to service_history
        Schema::rename('expenses_history', 'service_history');

        // Remove user_id column
        Schema::table('service_history', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
