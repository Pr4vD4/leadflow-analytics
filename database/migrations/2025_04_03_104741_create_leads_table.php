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
            $table->foreignId('company_id')->comment('ID компании')->constrained()->onDelete('cascade');
            $table->string('source')->comment('Источник заявки');
            $table->string('name')->nullable()->comment('Имя контакта');
            $table->string('email')->nullable()->comment('Email контакта');
            $table->string('phone')->nullable()->comment('Телефон контакта');
            $table->text('message')->nullable()->comment('Сообщение от клиента');
            $table->json('custom_fields')->nullable()->comment('Дополнительные поля в JSON формате');
            $table->enum('status', ['new', 'in_progress', 'completed', 'archived'])->default('new')->comment('Статус заявки');
            $table->string('category')->nullable()->comment('Категория заявки, определённая ИИ');
            $table->text('summary')->nullable()->comment('Краткое содержание заявки от ИИ');
            $table->text('generated_response')->nullable()->comment('Сгенерированный ИИ ответ');
            $table->integer('relevance_score')->nullable()->comment('Оценка релевантности от 1 до 10')
                  ->check('relevance_score BETWEEN 1 AND 10');
            $table->timestamps();

            // Добавляем индексы для ускорения поиска и фильтрации
            $table->index('source');
            $table->index('email');
            $table->index('phone');
            $table->index('name');
            $table->index('status');
            $table->index('category');
            $table->index('relevance_score');

            // Добавляем составной индекс для фильтрации по компании и дате
            $table->index(['company_id', 'created_at']);

            // Добавляем проверку, что email или phone должны быть заполнены
            $table->rawIndex("(CASE WHEN email IS NOT NULL THEN 1 WHEN phone IS NOT NULL THEN 1 ELSE 0 END)", 'leads_contact_info_idx');
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
