<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LeadRepository implements LeadRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function countByFilters(int $companyId, array $filters): int
    {
        return $this->buildFilteredQuery($companyId, $filters)->count();
    }

    /**
     * @inheritDoc
     */
    public function getGroupedStats(int $companyId, array $filters): array
    {
        $query = Lead::where('company_id', $companyId);
        $this->applyDateFilters($query, $filters);
        $this->applyTagFilter($query, $filters);
        $this->applySourceFilter($query, $filters);

        $result = $query->select('status', DB::raw('count(*) as count'))
                        ->groupBy('status')
                        ->get()
                        ->pluck('count', 'status')
                        ->toArray();

        // Преобразуем ключи в читаемые статусы
        $statuses = [
            'new' => 'Новая',
            'in_progress' => 'В работе',
            'completed' => 'Завершена',
            'archived' => 'В архиве'
        ];

        $formattedResult = [];
        foreach ($statuses as $key => $label) {
            $formattedResult[$label] = $result[$key] ?? 0;
        }

        return $formattedResult;
    }

    /**
     * @inheritDoc
     */
    public function getSourceDistribution(int $companyId, array $filters): array
    {
        $query = Lead::where('company_id', $companyId);
        $this->applyDateFilters($query, $filters);
        $this->applyStatusFilter($query, $filters);
        $this->applyTagFilter($query, $filters);

        return $query->select('source', DB::raw('count(*) as count'))
                    ->groupBy('source')
                    ->orderBy('count', 'desc')
                    ->get()
                    ->pluck('count', 'source')
                    ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getAvgRelevanceScore(int $companyId, array $filters): float
    {
        $query = $this->buildFilteredQuery($companyId, $filters);
        $result = $query->avg('relevance_score');

        return round($result ?? 0, 1);
    }

    /**
     * @inheritDoc
     */
    public function getTimeSeriesData(int $companyId, array $filters, string $groupBy): array
    {
        $query = Lead::where('company_id', $companyId);
        $this->applyStatusFilter($query, $filters);
        $this->applySourceFilter($query, $filters);
        $this->applyTagFilter($query, $filters);

        // Формат группировки по времени
        $format = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u', // Номер недели в году
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        // Формат для заголовков
        $labelFormat = match($groupBy) {
            'day' => 'd.m.Y',
            'week' => '\НW Y', // Неделя года
            'month' => 'm.Y',
            default => 'd.m.Y'
        };

        $dateField = DB::raw("DATE_FORMAT(created_at, '{$format}') as time_period");

        $result = $query->select($dateField, DB::raw('count(*) as count'))
                        ->groupBy('time_period')
                        ->orderBy('time_period')
                        ->get();

        $formattedData = [
            'labels' => [],
            'data' => []
        ];

        foreach ($result as $item) {
            try {
                // Преобразование формата для заголовков
                $date = $this->parseTimePeriod($item->time_period, $groupBy);
                $formattedData['labels'][] = $date->format($labelFormat);
                $formattedData['data'][] = $item->count;
            } catch (\Exception $e) {
                // Логируем ошибку и пропускаем невалидную запись
                \Illuminate\Support\Facades\Log::warning("Ошибка обработки даты: {$item->time_period}, " . $e->getMessage());
                continue;
            }
        }

        return $formattedData;
    }

    /**
     * Анализирует период времени и преобразует его в объект Carbon
     *
     * @param string $timePeriod Строка периода времени
     * @param string $groupBy Тип группировки (day, week, month)
     * @return \Carbon\Carbon
     */
    protected function parseTimePeriod(string $timePeriod, string $groupBy): \Carbon\Carbon
    {
        try {
            return match($groupBy) {
                'day' => $this->parseDayPeriod($timePeriod),
                'week' => $this->parseWeekPeriod($timePeriod),
                'month' => $this->parseMonthPeriod($timePeriod),
                default => $this->parseDayPeriod($timePeriod)
            };
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Ошибка парсинга периода: {$timePeriod}, " . $e->getMessage());
            return \Carbon\Carbon::now(); // Возвращаем текущую дату в случае ошибки
        }
    }

    /**
     * Парсит период дня в формате Y-m-d
     *
     * @param string $timePeriod
     * @return \Carbon\Carbon
     */
    protected function parseDayPeriod(string $timePeriod): \Carbon\Carbon
    {
        try {
            return \Carbon\Carbon::createFromFormat('Y-m-d', $timePeriod);
        } catch (\Exception $e) {
            // Пытаемся разобрать дату вручную
            $parts = explode('-', $timePeriod);
            $year = (int)($parts[0] ?? date('Y'));
            $month = (int)($parts[1] ?? 1);
            $day = (int)($parts[2] ?? 1);

            // Корректируем неправильные значения
            if ($month < 1) $month = 1;
            if ($month > 12) $month = 12;
            if ($day < 1) $day = 1;
            if ($day > 31) $day = 31;

            return \Carbon\Carbon::create($year, $month, $day);
        }
    }

    /**
     * Парсит период недели в формате Y-W
     *
     * @param string $timePeriod
     * @return \Carbon\Carbon
     */
    protected function parseWeekPeriod(string $timePeriod): \Carbon\Carbon
    {
        try {
            $parts = explode('-', $timePeriod);
            $year = (int)($parts[0] ?? date('Y'));
            $week = (int)($parts[1] ?? 1);

            // Корректируем неправильные значения
            if ($week < 1) $week = 1;
            if ($week > 53) $week = 53;

            return \Carbon\Carbon::now()->setISODate($year, $week);
        } catch (\Exception $e) {
            return \Carbon\Carbon::now();
        }
    }

    /**
     * Парсит период месяца в формате Y-m
     *
     * @param string $timePeriod
     * @return \Carbon\Carbon
     */
    protected function parseMonthPeriod(string $timePeriod): \Carbon\Carbon
    {
        try {
            $parts = explode('-', $timePeriod);
            $year = (int)($parts[0] ?? date('Y'));
            $month = (int)($parts[1] ?? 1);

            // Корректируем неправильные значения
            if ($month < 1) $month = 1;
            if ($month > 12) $month = 12;

            return \Carbon\Carbon::create($year, $month, 1);
        } catch (\Exception $e) {
            return \Carbon\Carbon::now();
        }
    }

    /**
     * @inheritDoc
     */
    public function getAllSources(int $companyId): array
    {
        return Lead::where('company_id', $companyId)
                  ->distinct()
                  ->pluck('source')
                  ->filter()
                  ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getFilteredLeads(int $companyId, array $filters)
    {
        $query = $this->buildFilteredQuery($companyId, $filters);

        return $query->with('tags')->get();
    }

    /**
     * Создает базовый запрос с применением всех фильтров
     *
     * @param int $companyId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function buildFilteredQuery(int $companyId, array $filters)
    {
        $query = Lead::where('company_id', $companyId);

        $this->applyDateFilters($query, $filters);
        $this->applyStatusFilter($query, $filters);
        $this->applySourceFilter($query, $filters);
        $this->applyTagFilter($query, $filters);

        return $query;
    }

    /**
     * Применяет фильтры по дате
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyDateFilters($query, array $filters): void
    {
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
    }

    /**
     * Применяет фильтр по статусу
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyStatusFilter($query, array $filters): void
    {
        if (isset($filters['status']) && $filters['status']) {
            $query->where('status', $filters['status']);
        }
    }

    /**
     * Применяет фильтр по источнику
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applySourceFilter($query, array $filters): void
    {
        if (isset($filters['source']) && $filters['source']) {
            $query->where('source', $filters['source']);
        }
    }

    /**
     * Применяет фильтр по тегу
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return void
     */
    protected function applyTagFilter($query, array $filters): void
    {
        if (isset($filters['tag']) && $filters['tag']) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('tags.id', $filters['tag']);
            });
        }
    }
}
