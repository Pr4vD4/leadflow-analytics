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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Имя пользователя');
            $table->string('email')->unique()->comment('Email пользователя для входа');
            $table->timestamp('email_verified_at')->nullable()->comment('Время подтверждения email');
            $table->string('password')->comment('Хэшированный пароль');
            $table->rememberToken()->comment('Токен запоминания сессии');
            $table->timestamps();

            // Добавляем индекс для ускорения поиска по имени
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
