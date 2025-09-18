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
        // MySQL may use the composite index for the vehicle_id FK; ensure a single-column index exists
        if (!Schema::hasColumn('vehicle_manuals', 'vehicle_id')) {
            return; // table is unexpected; bail
        }

        // Ensure single-column index exists to allow dropping the composite index safely
        try {
            \Illuminate\Support\Facades\DB::statement('CREATE INDEX IF NOT EXISTS vehicle_manuals_vehicle_id_index ON vehicle_manuals (vehicle_id)');
        } catch (\Throwable $e) {
            // Older MySQL may not support IF NOT EXISTS; attempt to create and ignore duplicate errors
            try {
                \Illuminate\Support\Facades\DB::statement('CREATE INDEX vehicle_manuals_vehicle_id_index ON vehicle_manuals (vehicle_id)');
            } catch (\Throwable $e2) {
                // ignore
            }
        }

        // Temporarily disable FK checks for index/column changes
        try { \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0'); } catch (\Throwable $e) {}

        // Drop composite index if present, then drop columns
        try {
            Schema::table('vehicle_manuals', function (Blueprint $table) {
                $table->dropIndex('vehicle_manuals_vehicle_id_section_id_index');
            });
        } catch (\Throwable $e) {
            // ignore if index already absent
        }

        Schema::table('vehicle_manuals', function (Blueprint $table) {
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

        try { \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1'); } catch (\Throwable $e) {}
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


