<div>
    <!-- Блок отображения комментариев -->
    <div class="bg-white dark:bg-secondary-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Комментарии</h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $comments->count() }} всего</span>
        </div>

        <!-- Список комментариев -->
        <div class="px-6 py-4">
            @if($comments->count() > 0)
                <div class="space-y-4">
                    @foreach($comments as $comment)
                        <div class="flex space-x-3 border border-gray-100 dark:border-secondary-700 rounded-lg p-4" wire:key="comment-{{ $comment->id }}">
                            <!-- Аватар пользователя -->
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-400">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <!-- Содержимое комментария -->
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $comment->user ? $comment->user->name : 'Система' }}
                                        </h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $comment->created_at->format('d.m.Y H:i') }}
                                            @if($comment->is_edited)
                                                · Отредактировано {{ $comment->edited_at->format('d.m.Y H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                    <!-- Кнопки действий -->
                                    @if($comment->user_id === auth()->id())
                                        <div class="flex space-x-2">
                                            <button
                                                wire:click="startEditing({{ $comment->id }})"
                                                class="text-gray-400 hover:text-primary-500 dark:hover:text-primary-400"
                                                title="Редактировать"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button
                                                wire:click="deleteComment({{ $comment->id }})"
                                                class="text-gray-400 hover:text-red-500 dark:hover:text-red-400"
                                                title="Удалить"
                                                onclick="return confirm('Вы уверены, что хотите удалить этот комментарий?')"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <!-- Режим редактирования -->
                                @if($editingCommentId === $comment->id)
                                    <div class="mt-2">
                                        <textarea
                                            wire:model="editedContent"
                                            class="w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                                            rows="3"
                                        ></textarea>
                                        <div class="mt-2 flex justify-end space-x-2">
                                            <button
                                                wire:click="cancelEditing"
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-secondary-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-secondary-700 hover:bg-gray-50 dark:hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800"
                                            >
                                                Отмена
                                            </button>
                                            <button
                                                wire:click="updateComment"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800"
                                            >
                                                Сохранить
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <!-- Текст комментария -->
                                    <div class="mt-1 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">
                                        {{ $comment->content }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-comments text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">Нет комментариев</p>
                </div>
            @endif
        </div>

        <!-- Форма добавления комментария -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-secondary-700">
            <form wire:submit.prevent="addComment">
                <div>
                    <label for="comment" class="sr-only">Комментарий</label>
                    <textarea
                        id="comment"
                        wire:model="newComment"
                        placeholder="Добавить комментарий..."
                        rows="3"
                        class="block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                    ></textarea>
                    @error('newComment') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div class="mt-3 flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800"
                    >
                        <i class="fas fa-paper-plane mr-2"></i>
                        Отправить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
