<?php

namespace App\Services\Analytics;

use App\Models\Lead;
use App\Models\Tag;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsService
{
    protected $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    /**
     * Получает статистику по заявкам в соответствии с фильтрами
     *
     * @param array $filters
     * @return array
     */
    public function getStatistics(array $filters): array
    {
        $companyId = Auth::user()->company_id;

        // Получаем основные метрики
        $totalLeads = $this->leadRepository->countByFilters($companyId, $filters);
        $leadsGrouped = $this->leadRepository->getGroupedStats($companyId, $filters);
        $conversionRate = $this->calculateConversionRate($companyId, $filters);
        $sourceDistribution = $this->leadRepository->getSourceDistribution($companyId, $filters);
        $relevanceScoreAvg = $this->leadRepository->getAvgRelevanceScore($companyId, $filters);

        // Если есть группировка, формируем данные для графика
        $chartData = [];
        if (isset($filters['group_by'])) {
            $chartData = $this->getChartData($companyId, $filters);
        }

        return [
            'total_leads' => $totalLeads,
            'leads_grouped' => $leadsGrouped,
            'conversion_rate' => $conversionRate,
            'source_distribution' => $sourceDistribution,
            'relevance_score_avg' => $relevanceScoreAvg,
            'chart_data' => $chartData,
        ];
    }

    /**
     * Рассчитывает конверсию (процент завершенных заявок)
     *
     * @param int $companyId
     * @param array $filters
     * @return float
     */
    protected function calculateConversionRate(int $companyId, array $filters): float
    {
        $totalLeads = $this->leadRepository->countByFilters($companyId, $filters);
        if ($totalLeads === 0) {
            return 0;
        }

        $completedLeads = $this->leadRepository->countByFilters($companyId, array_merge($filters, ['status' => 'completed']));

        return round(($completedLeads / $totalLeads) * 100, 2);
    }

    /**
     * Получает данные для графика
     *
     * @param int $companyId
     * @param array $filters
     * @return array
     */
    protected function getChartData(int $companyId, array $filters): array
    {
        $groupBy = $filters['group_by'] ?? 'day';

        return $this->leadRepository->getTimeSeriesData($companyId, $filters, $groupBy);
    }

    /**
     * Получает варианты для фильтрации
     *
     * @return array
     */
    public function getFilterOptions(): array
    {
        $companyId = Auth::user()->company_id;

        $statuses = [
            'new' => 'Новая',
            'in_progress' => 'В работе',
            'completed' => 'Завершена',
            'archived' => 'В архиве'
        ];

        $sources = $this->leadRepository->getAllSources($companyId);
        $tags = Tag::where('company_id', $companyId)->pluck('name', 'id')->toArray();

        $groupByOptions = [
            'day' => 'По дням',
            'week' => 'По неделям',
            'month' => 'По месяцам'
        ];

        return [
            'statuses' => $statuses,
            'sources' => $sources,
            'tags' => $tags,
            'group_by' => $groupByOptions
        ];
    }

    /**
     * Экспортирует данные в CSV
     *
     * @param array $filters
     * @return StreamedResponse
     */
    public function exportCsv(array $filters): StreamedResponse
    {
        $companyId = Auth::user()->company_id;
        $leads = $this->leadRepository->getFilteredLeads($companyId, $filters);

        return response()->streamDownload(function () use ($leads) {
            $handle = fopen('php://output', 'w');

            // Заголовки столбцов
            fputcsv($handle, [
                'ID', 'Источник', 'Имя', 'Email', 'Телефон',
                'Статус', 'Оценка релевантности', 'Создана', 'Теги'
            ]);

            // Данные
            foreach ($leads as $lead) {
                fputcsv($handle, [
                    $lead->id,
                    $lead->source,
                    $lead->name,
                    $lead->email,
                    $lead->phone,
                    $lead->status_label,
                    $lead->relevance_score,
                    $lead->created_at->format('d.m.Y H:i'),
                    $lead->tags->pluck('name')->implode(', ')
                ]);
            }

            fclose($handle);
        }, 'analytics_export_' . date('Y-m-d') . '.csv');
    }
}
