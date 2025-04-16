<?php

namespace App\Policies;

use App\Models\LeadComment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeadCommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Базовый доступ на просмотр, далее фильтруем по компании
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeadComment $leadComment): bool
    {
        // Пользователь может видеть комментарии только в рамках своей компании
        return $user->company_id === $leadComment->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Базовый доступ на создание, company_id проверяется при создании
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeadComment $leadComment): bool
    {
        // Пользователь может редактировать только свои комментарии и только в рамках своей компании
        return ($user->company_id === $leadComment->company_id) &&
               ($user->id === $leadComment->user_id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeadComment $leadComment): bool
    {
        // Пользователь может удалять только свои комментарии и только в рамках своей компании
        // Администраторы могут удалять любые комментарии в своей компании
        return ($user->company_id === $leadComment->company_id) &&
               ($user->id === $leadComment->user_id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LeadComment $leadComment): bool
    {
        // Аналогично удалению
        return ($user->company_id === $leadComment->company_id) &&
               ($user->id === $leadComment->user_id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LeadComment $leadComment): bool
    {
        // Только администраторы могут полностью удалять комментарии
        return ($user->company_id === $leadComment->company_id) && $user->hasRole('admin');
    }
}
