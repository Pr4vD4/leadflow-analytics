<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Отображает страницу аналитики
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'date_from',
            'date_to',
            'status',
            'source',
            'tag',
            'group_by'
        ]);

        // Устанавливаем значение по умолчанию для group_by, если оно не указано
        if (!isset($filters['group_by'])) {
            $filters['group_by'] = 'day';
        }

        $statistics = $this->analyticsService->getStatistics($filters);
        $filterOptions = $this->analyticsService->getFilterOptions();

        return view('crm.analytics.index', [
            'statistics' => $statistics,
            'filterOptions' => $filterOptions,
            'filters' => $filters,
        ]);
    }

    /**
     * Экспортирует данные аналитики в CSV формат
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Request $request)
    {
        $filters = $request->only([
            'date_from',
            'date_to',
            'status',
            'source',
            'tag'
        ]);

        return $this->analyticsService->exportCsv($filters);
    }
}
