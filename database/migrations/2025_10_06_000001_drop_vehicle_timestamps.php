<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (Schema::hasColumn('vehicles', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('vehicles', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('vehicles', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }
};


