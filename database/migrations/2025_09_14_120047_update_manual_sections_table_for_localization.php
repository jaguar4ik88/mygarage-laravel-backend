<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing records with default values
        DB::table('manual_sections')->update([
            'key' => DB::raw('id'),
            'icon' => 'settings',
            'is_active' => true,
        ]);
        
        // Make key unique and not null
        Schema::table('manual_sections', function (Blueprint $table) {
            $table->string('key')->unique()->change();
            $table->string('icon')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manual_sections', function (Blueprint $table) {
            $table->dropColumn(['key', 'icon', 'is_active', 'sort_order']);
        });
    }
};
