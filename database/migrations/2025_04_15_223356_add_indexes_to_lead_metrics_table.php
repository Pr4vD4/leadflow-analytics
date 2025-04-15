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
        Schema::table('lead_metrics', function (Blueprint $table) {
            // Индекс по дате расчета для быстрого поиска свежих метрик
            $table->index('calculated_at', 'lead_metrics_calculated_at_idx');

            // Составные индексы для часто используемых комбинаций полей
            $table->index(['company_id', 'period_type', 'calculated_at'], 'lead_metrics_company_period_calc_idx');

            // Индекс для фильтрации по источнику
            $table->index(['company_id', 'source'], 'lead_metrics_company_source_idx');

            // Индекс для быстрого поиска конверсии и средней релевантности
            $table->index('conversion_rate', 'lead_metrics_conversion_rate_idx');
            $table->index('avg_relevance_score', 'lead_metrics_relevance_score_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_metrics', function (Blueprint $table) {
            $table->dropIndex('lead_metrics_calculated_at_idx');
            $table->dropIndex('lead_metrics_company_period_calc_idx');
            $table->dropIndex('lead_metrics_company_source_idx');
            $table->dropIndex('lead_metrics_conversion_rate_idx');
            $table->dropIndex('lead_metrics_relevance_score_idx');
        });
    }
};
