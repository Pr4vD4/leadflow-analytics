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
        Schema::create('company_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique()->comment('Уникальный код приглашения');
            $table->foreignId('company_id')->comment('ID компании')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->comment('ID пользователя-создателя')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true)->comment('Флаг активности приглашения');
            $table->timestamp('activated_at')->nullable()->comment('Время активации приглашения');
            $table->foreignId('activated_by_user_id')->nullable()->comment('ID пользователя, активировавшего приглашение')->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Добавляем индексы для ускорения поиска
            $table->index('company_id');
            $table->index('user_id');
            $table->index('is_active');
            $table->index('activated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_invitations');
    }
};
