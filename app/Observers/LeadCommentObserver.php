<?php

namespace App\Observers;

use App\Models\LeadComment;

class LeadCommentObserver
{
    /**
     * Handle the LeadComment "created" event.
     */
    public function created(LeadComment $leadComment): void
    {
        // Записываем событие создания комментария
        $leadComment->lead->recordEvent(
            'comment_created',
            null,
            null,
            'Добавлен новый комментарий',
            [
                'comment_id' => $leadComment->id,
                'content' => $leadComment->content,
                'type' => 'comment' // Для фильтрации в истории изменений
            ]
        );
    }

    /**
     * Handle the LeadComment "updated" event.
     */
    public function updated(LeadComment $leadComment): void
    {
        // Если содержимое комментария изменилось
        if ($leadComment->isDirty('content')) {
            $oldContent = $leadComment->getOriginal('content');
            $newContent = $leadComment->content;

            // Записываем событие редактирования комментария
            $leadComment->lead->recordEvent(
                'comment_updated',
                $oldContent,
                $newContent,
                'Комментарий отредактирован',
                [
                    'comment_id' => $leadComment->id,
                    'type' => 'comment' // Для фильтрации в истории изменений
                ]
            );
        }
    }

    /**
     * Handle the LeadComment "deleted" event.
     */
    public function deleted(LeadComment $leadComment): void
    {
        // Записываем событие удаления комментария
        $leadComment->lead->recordEvent(
            'comment_deleted',
            $leadComment->content,
            null,
            'Комментарий удален',
            [
                'comment_id' => $leadComment->id,
                'type' => 'comment' // Для фильтрации в истории изменений
            ]
        );
    }

    /**
     * Handle the LeadComment "restored" event.
     */
    public function restored(LeadComment $leadComment): void
    {
        // Записываем событие восстановления комментария
        $leadComment->lead->recordEvent(
            'comment_restored',
            null,
            $leadComment->content,
            'Комментарий восстановлен',
            [
                'comment_id' => $leadComment->id,
                'type' => 'comment' // Для фильтрации в истории изменений
            ]
        );
    }

    /**
     * Handle the LeadComment "force deleted" event.
     */
    public function forceDeleted(LeadComment $leadComment): void
    {
        // Для полного удаления не записываем события, так как запись будет удалена из БД
    }
}
