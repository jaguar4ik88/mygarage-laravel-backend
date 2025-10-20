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
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // insurance, power_of_attorney, certificate, other
            $table->string('name'); // название документа
            $table->string('file_path'); // путь к файлу на сервере
            $table->string('file_name'); // оригинальное имя файла
            $table->integer('file_size')->nullable(); // размер в байтах
            $table->string('mime_type')->nullable(); // MIME тип
            $table->date('expiry_date')->nullable(); // дата истечения
            $table->text('notes')->nullable(); // заметки
            $table->timestamps();
            
            // Индексы
            $table->index(['vehicle_id', 'type']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
