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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->comment('ID компании пользователя')->constrained()->onDelete('set null');
            $table->boolean('is_admin')->default(false)->comment('Флаг администратора системы');

            // Добавляем индексы для ускорения поиска и фильтрации
            $table->index('company_id');
            $table->index('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id']);
            $table->dropIndex(['is_admin']);
            $table->dropColumn(['company_id', 'is_admin']);
        });
    }
};
