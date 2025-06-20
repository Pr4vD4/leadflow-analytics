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
        Schema::create('lead_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->comment('ID заявки')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->comment('ID тега')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Уникальный составной индекс для предотвращения дублирования связей
            $table->unique(['lead_id', 'tag_id']);

            // Индексы для ускорения поиска по связям
            $table->index('lead_id');
            $table->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_tag');
    }
};
