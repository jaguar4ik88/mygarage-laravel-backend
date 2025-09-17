<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_makers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('car_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_maker_id')->constrained('car_makers')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
            $table->unique(['car_maker_id', 'name']);
        });

        Schema::create('car_engines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_maker_id')->constrained('car_makers')->onDelete('cascade');
            $table->foreignId('car_model_id')->constrained('car_models')->onDelete('cascade');
            $table->string('label');
            $table->json('raw')->nullable();
            $table->timestamps();
            $table->unique(['car_maker_id', 'car_model_id', 'label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_engines');
        Schema::dropIfExists('car_models');
        Schema::dropIfExists('car_makers');
    }
};


