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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Название компании');
            $table->string('api_key')->unique()->comment('API ключ для интеграции');
            $table->string('email')->unique()->comment('Email компании');
            $table->string('phone')->nullable()->comment('Телефон компании');
            $table->text('description')->nullable()->comment('Описание компании');
            $table->string('telegram_chat_id')->nullable()->comment('ID чата Telegram для уведомлений');
            $table->boolean('is_active')->default(true)->comment('Статус активности компании');
            $table->timestamps();

            // Добавляем индексы для ускорения поиска и фильтрации
            $table->index('name');
            $table->index('api_key');
            $table->index('phone');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
