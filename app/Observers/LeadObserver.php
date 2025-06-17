<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\LeadMetric;
use App\Services\Bitrix24Service;
use Illuminate\Support\Facades\Log;

class LeadObserver
{
    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead): void
    {
        // Записываем событие создания заявки
        $lead->recordEvent(
            'created',
            null,
            null,
            'Заявка создана через ' . ($lead->source ? 'источник "' . $lead->source . '"' : 'неизвестный источник')
        );

        // Устанавливаем время изменения статуса
        $lead->status_changed_at = now();
        $lead->saveQuietly(); // Сохраняем без повторного вызова обсервера

        // Отправляем лид в Битрикс24 если интеграция включена
        $this->sendToBitrix24($lead);

        // Обновляем метрики для компании после создания заявки
        $this->updateMetricsForCompany($lead->company_id);
    }

    /**
     * Handle the Lead "updated" event.
     */
    public function updated(Lead $lead): void
    {
        // Отслеживаем изменение статуса
        if ($lead->isDirty('status')) {
            $oldStatus = $lead->getOriginal('status');
            $newStatus = $lead->status;

            // Записываем событие изменения статуса с локализованными названиями
            $lead->recordEvent(
                'status_changed',
                $oldStatus,
                $newStatus,
                "Статус изменен с '" . $lead->getStatusLabel($oldStatus) . "' на '" . $lead->getStatusLabel($newStatus) . "'"
            );

            // Обновляем время изменения статуса
            $lead->status_changed_at = now();

            // Первый ответ (когда заявка переходит из статуса "новая")
            if ($oldStatus === 'new' && !$lead->first_response_at) {
                $lead->first_response_at = now();
                $lead->response_time_minutes = $lead->first_response_at->diffInMinutes($lead->created_at);
            }

            // Завершение заявки
            if ($newStatus === 'completed' && !$lead->resolved_at) {
                $lead->resolved_at = now();
                $lead->resolution_time_minutes = $lead->resolved_at->diffInMinutes($lead->created_at);
            }

            // Сохраняем изменения без повторного вызова обсервера
            $lead->saveQuietly();

            // Обновляем метрики при изменении статуса заявки
            $this->updateMetricsForCompany($lead->company_id);
        }

        // Отслеживаем изменение других важных полей
        $trackedFields = [
            'name' => 'Имя',
            'email' => 'Email',
            'phone' => 'Телефон',
            'message' => 'Сообщение',
            'source' => 'Источник',
            'category' => 'Категория',
            'relevance_score' => 'Оценка релевантности'
        ];
        $shouldUpdateMetrics = false;

        foreach ($trackedFields as $field => $fieldLabel) {
            if ($lead->isDirty($field)) {
                $oldValue = $lead->getOriginal($field);
                $newValue = $lead->{$field};

                // Записываем событие изменения поля с локализованным названием
                $lead->recordEvent(
                    'field_updated',
                    $oldValue,
                    $newValue,
                    "Поле '$fieldLabel' изменено"
                );

                // Если изменилась оценка релевантности или источник, нужно обновить метрики
                if (in_array($field, ['relevance_score', 'source'])) {
                    $shouldUpdateMetrics = true;
                }
            }
        }

        // Обновляем метрики, если изменились важные поля
        if ($shouldUpdateMetrics) {
            $this->updateMetricsForCompany($lead->company_id);
        }
    }

    /**
     * Handle the Lead "deleted" event.
     */
    public function deleted(Lead $lead): void
    {
        // Записываем событие удаления заявки
        $lead->recordEvent(
            'deleted',
            null,
            null,
            'Заявка удалена из системы'
        );

        // Обновляем метрики после удаления заявки
        $this->updateMetricsForCompany($lead->company_id);
    }

    /**
     * Handle the Lead "restored" event.
     */
    public function restored(Lead $lead): void
    {
        // Записываем событие восстановления заявки
        $lead->recordEvent(
            'restored',
            null,
            null,
            'Заявка восстановлена из удаленных'
        );

        // Обновляем метрики после восстановления заявки
        $this->updateMetricsForCompany($lead->company_id);
    }

    /**
     * Handle the Lead "force deleted" event.
     */
    public function forceDeleted(Lead $lead): void
    {
        // Ничего не делаем, так как запись полностью удалена из БД
    }

    /**
     * Обновляет метрики для компании
     *
     * @param int $companyId
     * @return void
     */
    private function updateMetricsForCompany(int $companyId): void
    {
        // Обновляем только метрики текущего дня и недели для оптимизации производительности
        $now = now();
        $periods = [
            'daily' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'weekly' => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
            ]
        ];

        foreach ($periods as $periodType => $period) {
            // Обновляем общие метрики (без фильтрации по источнику)
            LeadMetric::calculateMetrics(
                $companyId,
                $periodType,
                $period['start']->toDateString(),
                $period['end']->toDateString(),
                null // без указания источника
            );
        }
    }

    /**
     * Обрабатывает события изменения тегов заявки.
     *
     * @param  \App\Models\Lead  $lead
     * @param  array  $oldTagIds
     * @param  array  $newTagIds
     * @return void
     */
    public function updatedTags(Lead $lead, array $oldTagIds, array $newTagIds): void
    {
        $oldTags = \App\Models\Tag::whereIn('id', $oldTagIds)->pluck('name')->toArray();
        $newTags = \App\Models\Tag::whereIn('id', $newTagIds)->pluck('name')->toArray();

        $addedTags = array_values(array_diff($newTagIds, $oldTagIds));
        $removedTags = array_values(array_diff($oldTagIds, $newTagIds));

        // Если были добавлены новые теги
        if (!empty($addedTags)) {
            $addedTagNames = \App\Models\Tag::whereIn('id', $addedTags)->pluck('name')->implode(', ');
            $lead->recordEvent(
                'tags_added',
                null,
                $addedTagNames,
                'Добавлены теги: ' . $addedTagNames
            );
        }

        // Если были удалены теги
        if (!empty($removedTags)) {
            $removedTagNames = \App\Models\Tag::whereIn('id', $removedTags)->pluck('name')->implode(', ');
            $lead->recordEvent(
                'tags_removed',
                $removedTagNames,
                null,
                'Удалены теги: ' . $removedTagNames
            );
        }

        // Если были и добавлены и удалены теги, записываем общее событие обновления
        if (!empty($addedTags) && !empty($removedTags)) {
            $oldTagsStr = empty($oldTags) ? 'нет тегов' : implode(', ', $oldTags);
            $newTagsStr = empty($newTags) ? 'нет тегов' : implode(', ', $newTags);

            $lead->recordEvent(
                'tags_updated',
                $oldTagsStr,
                $newTagsStr,
                'Обновлены теги заявки'
            );
        }
    }

    /**
     * Отправляет лид в Битрикс24 через интеграцию
     *
     * @param Lead $lead
     * @return void
     */
    private function sendToBitrix24(Lead $lead): void
    {
        try {
            $bitrix24Service = app(Bitrix24Service::class);
            $result = $bitrix24Service->sendLead($lead);

            if ($result) {
                // Записываем событие успешной отправки в Битрикс24
                $lead->recordEvent(
                    'bitrix24_sent',
                    null,
                    null,
                    'Лид успешно отправлен в Битрикс24'
                );
            }
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            Log::error('Ошибка при отправке лида в Битрикс24: ' . $e->getMessage(), [
                'lead_id' => $lead->id,
                'company_id' => $lead->company_id,
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            // Записываем событие ошибки отправки в Битрикс24
            $lead->recordEvent(
                'bitrix24_error',
                null,
                null,
                'Ошибка при отправке лида в Битрикс24: ' . $e->getMessage()
            );
        }
    }
}
