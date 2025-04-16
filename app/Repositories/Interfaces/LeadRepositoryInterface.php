<?php

namespace App\Repositories\Interfaces;

interface LeadRepositoryInterface
{
    /**
     * Подсчитывает количество заявок по фильтрам
     *
     * @param int $companyId
     * @param array $filters
     * @return int
     */
    public function countByFilters(int $companyId, array $filters): int;

    /**
     * Получает статистику по заявкам, сгруппированную по статусам
     *
     * @param int $companyId
     * @param array $filters
     * @return array
     */
    public function getGroupedStats(int $companyId, array $filters): array;

    /**
     * Получает распределение заявок по источникам
     *
     * @param int $companyId
     * @param array $filters
     * @return array
     */
    public function getSourceDistribution(int $companyId, array $filters): array;

    /**
     * Получает среднюю оценку релевантности заявок
     *
     * @param int $companyId
     * @param array $filters
     * @return float
     */
    public function getAvgRelevanceScore(int $companyId, array $filters): float;

    /**
     * Получает данные о заявках для временного ряда
     *
     * @param int $companyId
     * @param array $filters
     * @param string $groupBy
     * @return array
     */
    public function getTimeSeriesData(int $companyId, array $filters, string $groupBy): array;

    /**
     * Получает все источники заявок компании
     *
     * @param int $companyId
     * @return array
     */
    public function getAllSources(int $companyId): array;

    /**
     * Получает отфильтрованные заявки компании
     *
     * @param int $companyId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFilteredLeads(int $companyId, array $filters);
}
