<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadMetric;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Конструктор контроллера дашборда
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Отображает страницу дашборда
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Получаем компанию текущего пользователя
        $companyId = auth()->user()->company_id;

        // Получаем параметры фильтров
        $period = $request->input('period', 'week');
        $source = $request->input('source', 'all');

        // Определяем даты периода
        [$startDate, $endDate, $periodType] = $this->getPeriodDates($period);

        // Получаем или рассчитываем метрики
        $metrics = $this->getOrCalculateMetrics($companyId, $periodType, $startDate, $endDate, $source);

        // Получаем данные для графика динамики заявок
        $leadsTrend = $this->getLeadsTrend($companyId, $startDate, $endDate, $source);

        // Получаем последние заявки
        $recentLeads = Lead::forCompany($companyId)
            ->with('tags')
            ->latest()
            ->take(5)
            ->get();

        return view('crm.dashboard.index', compact(
            'metrics',
            'leadsTrend',
            'recentLeads',
            'period',
            'source'
        ));
    }

    /**
     * Получает даты периода на основе выбранного периода
     *
     * @param  string  $period
     * @return array
     */
    private function getPeriodDates($period)
    {
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                return [$now->startOfDay(), $now->copy()->endOfDay(), 'daily'];
            case 'yesterday':
                return [$now->subDay()->startOfDay(), $now->copy()->endOfDay(), 'daily'];
            case 'week':
                return [$now->startOfWeek(), $now->copy()->endOfWeek(), 'weekly'];
            case 'month':
                return [$now->startOfMonth(), $now->copy()->endOfMonth(), 'monthly'];
            case 'quarter':
                return [$now->startOfQuarter(), $now->copy()->endOfQuarter(), 'quarterly'];
            case 'year':
                return [$now->startOfYear(), $now->copy()->endOfYear(), 'yearly'];
            default:
                return [$now->startOfWeek(), $now->copy()->endOfWeek(), 'weekly'];
        }
    }

    /**
     * Получает или рассчитывает метрики для заданного периода
     *
     * @param  int  $companyId
     * @param  string  $periodType
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @param  string  $source
     * @return array
     */
    private function getOrCalculateMetrics($companyId, $periodType, $startDate, $endDate, $source)
    {
        // Преобразуем 'all' в null для поиска метрик по всем источникам
        $sourceFilter = $source === 'all' ? null : $source;

        // Пытаемся найти уже рассчитанные метрики
        $cachedMetric = LeadMetric::forCompany($companyId)
            ->forPeriod($periodType, $startDate->toDateString(), $sourceFilter)
            ->where('calculated_at', '>', now()->subHours(1)) // Не старше 1 часа
            ->first();

        // Если есть актуальные метрики, используем их
        if ($cachedMetric) {
            return [
                'total_leads' => $cachedMetric->total_leads,
                'conversion_rate' => $cachedMetric->conversion_rate,
                'avg_relevance_score' => $cachedMetric->avg_relevance_score,
                'avg_response_time' => $cachedMetric->avg_response_time,
                'source_distribution' => $cachedMetric->source_distribution,
            ];
        }

        // Иначе рассчитываем и сохраняем новые метрики
        $metric = LeadMetric::calculateMetrics(
            $companyId,
            $periodType,
            $startDate->toDateString(),
            $endDate->toDateString(),
            $sourceFilter
        );

        return [
            'total_leads' => $metric->total_leads,
            'conversion_rate' => $metric->conversion_rate,
            'avg_relevance_score' => $metric->avg_relevance_score,
            'avg_response_time' => $metric->avg_response_time,
            'source_distribution' => $metric->source_distribution,
        ];
    }

    /**
     * Получает данные для графика динамики заявок
     *
     * @param  int  $companyId
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @param  string  $source
     * @return array
     */
    private function getLeadsTrend($companyId, $startDate, $endDate, $source)
    {
        // Базовый запрос
        $query = Lead::forCompany($companyId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Фильтрация по источнику
        if ($source !== 'all') {
            $query->where('source', $source);
        }

        // Определяем формат группировки по дате
        $dateFormat = $this->getDateFormat($startDate, $endDate);

        // Получаем данные с группировкой по дате
        $trend = $query->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as date"),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Подготавливаем данные для графика
        $dates = [];
        $counts = [];
        $completed = [];
        $conversion = [];

        foreach ($trend as $point) {
            $dates[] = $point->date;
            $counts[] = $point->count;
            $completed[] = $point->completed;
            $conversion[] = $point->count > 0 ? round(($point->completed / $point->count) * 100, 1) : 0;
        }

        return [
            'dates' => $dates,
            'counts' => $counts,
            'completed' => $completed,
            'conversion' => $conversion,
        ];
    }

    /**
     * Возвращает формат даты для группировки в зависимости от периода
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return string
     */
    private function getDateFormat($startDate, $endDate)
    {
        $diffInDays = $startDate->diffInDays($endDate);

        if ($diffInDays <= 2) {
            return '%Y-%m-%d %H:00'; // По часам
        } elseif ($diffInDays <= 31) {
            return '%Y-%m-%d'; // По дням
        } elseif ($diffInDays <= 90) {
            return '%Y-%m-%d'; // По дням для квартала
        } else {
            return '%Y-%m'; // По месяцам для года
        }
    }

    /**
     * Экспортирует данные метрик в CSV
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCsv(Request $request)
    {
        // Получаем компанию текущего пользователя
        $companyId = auth()->user()->company_id;

        // Получаем параметры фильтров
        $period = $request->input('period', 'week');
        $source = $request->input('source', 'all');

        // Определяем даты периода
        [$startDate, $endDate, $periodType] = $this->getPeriodDates($period);

        // Запрос для получения данных
        $query = Lead::forCompany($companyId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($source !== 'all') {
            $query->where('source', $source);
        }

        // Получаем данные
        $leads = $query->select(
                'id',
                'source',
                'name',
                'email',
                'phone',
                'status',
                'relevance_score',
                'created_at',
                'first_response_at',
                'resolved_at',
                'response_time_minutes',
                'resolution_time_minutes'
            )
            ->get();

        // Подготавливаем имя файла
        $filename = 'leads_export_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        // Создаем временный файл с кодировкой UTF-8 и BOM-маркером
        $handle = fopen(storage_path('app/' . $filename), 'w');

        // Добавляем BOM-маркер UTF-8 в начало файла для правильного определения кодировки Excel
        fputs($handle, "\xEF\xBB\xBF");

        // Заголовки CSV
        fputcsv($handle, [
            'ID',
            'Источник',
            'Имя',
            'Email',
            'Телефон',
            'Статус',
            'Оценка релевантности',
            'Дата создания',
            'Первый ответ',
            'Завершение',
            'Время ответа (мин)',
            'Время резолюции (мин)',
        ], ';'); // Используем разделитель ";" для лучшей совместимости с Excel

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
                $lead->created_at->format('Y-m-d H:i:s'),
                $lead->first_response_at ? $lead->first_response_at->format('Y-m-d H:i:s') : '-',
                $lead->resolved_at ? $lead->resolved_at->format('Y-m-d H:i:s') : '-',
                $lead->response_time_minutes ?? '-',
                $lead->resolution_time_minutes ?? '-',
            ], ';'); // Используем разделитель ";" для лучшей совместимости с Excel
        }

        fclose($handle);

        // Возвращаем файл с указанием кодировки
        return response()->download(
            storage_path('app/' . $filename),
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]
        )->deleteFileAfterSend();
    }
}
