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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('source');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->json('custom_fields')->nullable();
            $table->enum('status', ['new', 'in_progress', 'completed', 'archived'])->default('new');
            $table->string('category')->nullable(); // Для ИИ категоризации
            $table->text('summary')->nullable(); // Для ИИ суммаризации
            $table->text('generated_response')->nullable(); // Для ИИ генерации ответов
            $table->integer('relevance_score')->nullable(); // Оценка релевантности от 1 до 10
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
