<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadMetric extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'period_type',
        'period_start',
        'period_end',
        'source',
        'status',
        'total_leads',
        'new_leads',
        'in_progress_leads',
        'completed_leads',
        'archived_leads',
        'conversion_rate',
        'avg_response_time',
        'avg_resolution_time',
        'avg_relevance_score',
        'source_distribution',
        'custom_metrics',
        'calculated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'conversion_rate' => 'float',
        'avg_response_time' => 'float',
        'avg_resolution_time' => 'float',
        'avg_relevance_score' => 'float',
        'source_distribution' => 'array',
        'custom_metrics' => 'array',
        'calculated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the metric.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope a query to only include metrics for a specific company.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Get metrics for a specific period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $periodType
     * @param  string  $periodStart
     * @param  string|null  $source
     * @param  string|null  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPeriod($query, $periodType, $periodStart, $source = null, $status = null)
    {
        $query = $query->where('period_type', $periodType)
                       ->where('period_start', $periodStart);

        if ($source) {
            $query->where('source', $source);
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query;
    }

    /**
     * Calculate and save metrics for a given period.
     *
     * @param  int  $companyId
     * @param  string  $periodType
     * @param  string  $periodStart
     * @param  string  $periodEnd
     * @param  string|null  $source
     * @param  string|null  $status
     * @return self
     */
    public static function calculateMetrics($companyId, $periodType, $periodStart, $periodEnd, $source = null, $status = null)
    {
        // Базовый запрос для фильтрации заявок
        $query = Lead::forCompany($companyId)
                    ->whereBetween('created_at', [$periodStart, $periodEnd]);

        if ($source) {
            $query->where('source', $source);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Рассчитываем метрики
        $totalLeads = $query->count();
        $newLeads = $query->where('status', 'new')->count();
        $inProgressLeads = $query->where('status', 'in_progress')->count();
        $completedLeads = $query->where('status', 'completed')->count();
        $archivedLeads = $query->where('status', 'archived')->count();

        // Конверсия: процент завершенных заявок от общего числа
        $conversionRate = $totalLeads > 0 ? ($completedLeads / $totalLeads) * 100 : 0;

        // Среднее время ответа в минутах
        $avgResponseTime = $query->whereNotNull('response_time_minutes')->avg('response_time_minutes');

        // Среднее время резолюции в минутах
        $avgResolutionTime = $query->whereNotNull('resolution_time_minutes')->avg('resolution_time_minutes');

        // Средний балл релевантности
        $avgRelevanceScore = $query->whereNotNull('relevance_score')->avg('relevance_score');

        // Получаем распределение по источникам
        $sourceDistribution = null;
        if (!$source) {
            $sourceDistribution = Lead::forCompany($companyId)
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->selectRaw('source, count(*) as count')
                ->groupBy('source')
                ->pluck('count', 'source')
                ->toArray();
        }

        // Создаем или обновляем запись метрик
        return self::updateOrCreate(
            [
                'company_id' => $companyId,
                'period_type' => $periodType,
                'period_start' => $periodStart,
                'source' => $source,
                'status' => $status,
            ],
            [
                'period_end' => $periodEnd,
                'total_leads' => $totalLeads,
                'new_leads' => $newLeads,
                'in_progress_leads' => $inProgressLeads,
                'completed_leads' => $completedLeads,
                'archived_leads' => $archivedLeads,
                'conversion_rate' => $conversionRate,
                'avg_response_time' => $avgResponseTime,
                'avg_resolution_time' => $avgResolutionTime,
                'avg_relevance_score' => $avgRelevanceScore,
                'source_distribution' => $sourceDistribution,
                'calculated_at' => now(),
            ]
        );
    }
}
