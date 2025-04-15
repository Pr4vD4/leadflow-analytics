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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->comment('ID компании')->constrained()->onDelete('cascade');
            $table->string('name')->comment('Название тега');
            $table->string('color')->default('#6366F1')->comment('Цвет тега в HEX формате');
            $table->timestamps();

            // Уникальный составной индекс для названия тега в рамках компании
            $table->unique(['company_id', 'name']);

            // Индекс для ускорения поиска по имени тега
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
