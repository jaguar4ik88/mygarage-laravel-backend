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
        Schema::table('expense_types', function (Blueprint $table) {
            $table->foreignId('translation_group_id')->nullable()->after('is_active')
                ->constrained('translation_groups')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_types', function (Blueprint $table) {
            $table->dropConstrainedForeignId('translation_group_id');
        });
    }
};
