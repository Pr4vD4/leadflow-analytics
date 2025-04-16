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
        Schema::create('lead_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->comment('ID заявки')->constrained()->onDelete('cascade');
            $table->text('generated_response')->nullable()->comment('Сгенерированный ИИ ответ');
            $table->json('analysis_data')->nullable()->comment('Данные анализа в JSON формате');
            $table->string('sentiment')->nullable()->comment('Тональность сообщения');
            $table->integer('urgency_score')->nullable()->comment('Оценка срочности от 1 до 10');
            $table->integer('complexity_score')->nullable()->comment('Оценка сложности от 1 до 10');
            $table->text('key_points')->nullable()->comment('Ключевые моменты заявки');
            $table->string('ai_model_used')->nullable()->comment('Использованная модель ИИ');
            $table->string('processing_status')->default('pending')->comment('Статус обработки');
            $table->timestamps();

            // Добавляем индексы
            $table->index('lead_id');
            $table->index('processing_status');
            $table->index('sentiment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_analytics');
    }
};
