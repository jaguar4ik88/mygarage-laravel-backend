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
        Schema::table('vehicle_manuals', function (Blueprint $table) {
            // Drop composite index created in initial table (vehicle_id, section_id)
            try {
                $table->dropIndex('vehicle_manuals_vehicle_id_section_id_index');
            } catch (\Throwable $e) {
                // Index might not exist in some environments; ignore
            }
            if (Schema::hasColumn('vehicle_manuals', 'section_id')) {
                $table->dropColumn('section_id');
            }
            if (Schema::hasColumn('vehicle_manuals', 'icon')) {
                $table->dropColumn('icon');
            }
            if (Schema::hasColumn('vehicle_manuals', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            if (Schema::hasColumn('vehicle_manuals', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_manuals', function (Blueprint $table) {
            $table->string('section_id')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            // Recreate composite index
            $table->index(['vehicle_id', 'section_id']);
        });
    }
};


