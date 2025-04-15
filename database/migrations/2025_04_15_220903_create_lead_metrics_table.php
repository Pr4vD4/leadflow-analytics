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
        Schema::create('lead_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('period_type'); // daily, weekly, monthly, yearly
            $table->date('period_start');
            $table->date('period_end');
            $table->string('source')->nullable(); // Если null, то для всех источников
            $table->string('status')->nullable(); // Если null, то для всех статусов
            $table->integer('total_leads')->default(0);
            $table->integer('new_leads')->default(0);
            $table->integer('in_progress_leads')->default(0);
            $table->integer('completed_leads')->default(0);
            $table->integer('archived_leads')->default(0);
            $table->float('conversion_rate')->default(0); // В процентах
            $table->float('avg_response_time')->nullable(); // В минутах
            $table->float('avg_resolution_time')->nullable(); // В минутах
            $table->float('avg_relevance_score')->nullable();
            $table->json('source_distribution')->nullable(); // JSON с распределением по источникам
            $table->json('custom_metrics')->nullable(); // Для дополнительных метрик
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->unique(['company_id', 'period_type', 'period_start', 'source', 'status'], 'metrics_uniqueness_idx');
            $table->index(['company_id', 'period_type', 'period_start'], 'metrics_period_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_metrics');
    }
};
