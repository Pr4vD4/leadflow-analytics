<?php

namespace App\Observers;

use App\Models\Lead;

class LeadObserver
{
    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead): void
    {
        // Записываем событие создания заявки
        $lead->recordEvent('created', null, null, 'Заявка создана');

        // Устанавливаем время изменения статуса
        $lead->status_changed_at = now();
        $lead->saveQuietly(); // Сохраняем без повторного вызова обсервера
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

            // Записываем событие изменения статуса
            $lead->recordEvent('status_changed', $oldStatus, $newStatus, "Статус изменен с '$oldStatus' на '$newStatus'");

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
        }

        // Отслеживаем изменение других важных полей
        $trackedFields = ['name', 'email', 'phone', 'message', 'source', 'category', 'relevance_score'];

        foreach ($trackedFields as $field) {
            if ($lead->isDirty($field)) {
                $oldValue = $lead->getOriginal($field);
                $newValue = $lead->{$field};

                // Записываем событие изменения поля
                $lead->recordEvent(
                    'field_updated',
                    $oldValue,
                    $newValue,
                    "Поле '$field' изменено"
                );
            }
        }
    }

    /**
     * Handle the Lead "deleted" event.
     */
    public function deleted(Lead $lead): void
    {
        // Записываем событие удаления заявки
        $lead->recordEvent('deleted', null, null, 'Заявка удалена');
    }

    /**
     * Handle the Lead "restored" event.
     */
    public function restored(Lead $lead): void
    {
        // Записываем событие восстановления заявки
        $lead->recordEvent('restored', null, null, 'Заявка восстановлена');
    }

    /**
     * Handle the Lead "force deleted" event.
     */
    public function forceDeleted(Lead $lead): void
    {
        // Ничего не делаем, так как запись полностью удалена из БД
    }
}
