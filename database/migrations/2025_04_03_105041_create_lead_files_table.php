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
        Schema::create('lead_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->comment('ID заявки')->constrained()->onDelete('cascade');
            $table->string('filename')->comment('Имя файла в системе');
            $table->string('original_filename')->comment('Оригинальное имя файла');
            $table->string('filepath')->comment('Путь к файлу в хранилище');
            $table->string('mime_type')->comment('MIME-тип файла');
            $table->integer('size')->comment('Размер файла в байтах')->check('size > 0');
            $table->timestamps();

            // Индексы для ускорения поиска
            $table->index('lead_id');
            $table->index('mime_type');
            $table->index('original_filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_files');
    }
};
