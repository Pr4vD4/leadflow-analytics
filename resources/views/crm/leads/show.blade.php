@extends('layouts.crm')

@section('title', 'Просмотр заявки #' . $lead->id)

@section('content')
    <div class="container mx-auto">
        <!-- Верхняя панель с навигацией и действиями -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-2">
                <a href="{{ route('crm.leads.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Заявка #{{ $lead->id }}</h1>

                <!-- Статус -->
                @if($lead->status === 'new')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ $lead->status_label }}
                    </span>
                @elseif($lead->status === 'in_progress')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                        {{ $lead->status_label }}
                    </span>
                @elseif($lead->status === 'completed')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        {{ $lead->status_label }}
                    </span>
                @elseif($lead->status === 'archived')
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                        {{ $lead->status_label }}
                    </span>
                @endif
            </div>

            <!-- Кнопки действий -->
            <div class="flex space-x-2">
                <!-- Форма изменения статуса -->
                <form method="POST" action="{{ route('crm.leads.update-status', $lead->id) }}" class="flex sm:ml-3">
                    @csrf
                    <select name="status" class="block w-48 rounded-l-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>Новая</option>
                        <option value="in_progress" {{ $lead->status === 'in_progress' ? 'selected' : '' }}>В работе</option>
                        <option value="completed" {{ $lead->status === 'completed' ? 'selected' : '' }}>Завершена</option>
                        <option value="archived" {{ $lead->status === 'archived' ? 'selected' : '' }}>В архиве</option>
                    </select>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                        Изменить
                    </button>
                </form>
            </div>
        </div>

        <!-- Сообщения об ошибках и уведомления -->
        @if(session('success'))
            <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-4 rounded-md mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Табы -->
        <div class="mb-6">
            <div class="border-b border-gray-200 dark:border-secondary-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#tab-details" class="tab-link active whitespace-nowrap py-4 px-1 border-b-2 border-primary-500 dark:border-primary-400 text-sm font-medium text-primary-600 dark:text-primary-400" data-tab="tab-details">
                        Детали заявки
                    </a>
                    <a href="#tab-comments" class="tab-link whitespace-nowrap py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600" data-tab="tab-comments">
                        Комментарии
                    </a>
                    <a href="#tab-history" class="tab-link whitespace-nowrap py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600" data-tab="tab-history">
                        История изменений
                    </a>
                    <a href="#tab-edit" class="tab-link whitespace-nowrap py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600" data-tab="tab-edit">
                        Редактирование
                    </a>
                </nav>
            </div>
        </div>

        <!-- Содержимое табов -->
        <div id="tab-details" class="tab-content block">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Основная информация о заявке -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Информация о контакте -->
                    <div class="bg-white dark:bg-secondary-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Контактная информация</h3>
                        </div>
                        <div class="px-6 py-4">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Имя</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $lead->name ?: 'Не указано' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $lead->email ?: 'Не указан' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Телефон</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $lead->phone ?: 'Не указан' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Источник</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $lead->source }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Сообщение клиента -->
                    <div class="bg-white dark:bg-secondary-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Сообщение</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="bg-gray-50 dark:bg-secondary-700 rounded-md p-4">
                                <p class="text-sm text-gray-900 dark:text-white">{{ $lead->message ?: 'Нет сообщения' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Файлы (если есть) -->
                    @if($lead->files->count() > 0)
                        <div class="bg-white dark:bg-secondary-800 shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Прикрепленные файлы</h3>
                            </div>
                            <div class="px-6 py-4">
                                <ul class="divide-y divide-gray-200 dark:divide-secondary-700">
                                    @foreach($lead->files as $file)
                                        <li class="py-2 flex justify-between items-center">
                                            <div class="flex items-center">
                                                <i class="fas {{ Str::endsWith($file->path, ['.jpg', '.jpeg', '.png', '.gif']) ? 'fa-image' : 'fa-file' }} text-gray-400 mr-3"></i>
                                                <span class="text-sm text-gray-900 dark:text-white">{{ basename($file->path) }}</span>
                                            </div>
                                            <a href="#" class="text-primary-600 dark:text-primary-500 hover:text-primary-900 dark:hover:text-primary-400">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Боковая панель с дополнительной информацией и ИИ-аналитикой -->
                <div class="space-y-6">
                    <!-- Метаданные о заявке -->
                    <div class="bg-white dark:bg-secondary-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Информация</h3>
                        </div>
                        <div class="px-6 py-4">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Дата создания</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $lead->created_at->format('d.m.Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Последнее обновление</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $lead->updated_at->format('d.m.Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Релевантность</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        @if($lead->relevance_score)
                                            <span class="inline-flex items-center">
                                                {{ $lead->relevance_score }}/10
                                                <i class="fas fa-star text-yellow-400 ml-1"></i>
                                            </span>
                                            <form method="POST" action="{{ route('crm.leads.update-relevance', $lead->id) }}" class="inline-block ml-2">
                                                @csrf
                                                <button type="submit" class="text-primary-600 dark:text-primary-400 text-xs hover:text-primary-800 dark:hover:text-primary-300">
                                                    <i class="fas fa-sync-alt"></i> Переоценить
                                                </button>
                                            </form>

                                            @if($lead->analytics && $lead->analytics->relevance_explanation)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 bg-gray-50 dark:bg-secondary-700 p-2 rounded-md">
                                                    <span class="font-medium text-gray-700 dark:text-gray-300">Обоснование оценки:</span>
                                                    {{ $lead->analytics->relevance_explanation }}
                                                </p>
                                            @endif
                                        @else
                                            <div class="flex items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Не оценено</span>
                                                <form method="POST" action="{{ route('crm.leads.update-relevance', $lead->id) }}" class="inline-block ml-2">
                                                    @csrf
                                                    <button type="submit" class="text-primary-600 dark:text-primary-400 text-xs hover:text-primary-800 dark:hover:text-primary-300">
                                                        <i class="fas fa-magic"></i> Оценить ИИ
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Теги</dt>
                                    <dd class="mt-1">
                                        @if($lead->tags->count() > 0)
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($lead->tags as $tag)
                                                    <a href="{{ route('crm.leads.index', ['filter' => ['tag' => $tag->id]]) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300 hover:bg-primary-200 dark:hover:bg-primary-800 transition-colors">
                                                        <i class="fas fa-tag mr-1 text-primary-500 dark:text-primary-400"></i>
                                                        {{ $tag->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Нет тегов</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- ИИ-аналитика -->
                    <div class="bg-white dark:bg-secondary-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">ИИ-аналитика</h3>
                        </div>
                        <div class="px-6 py-4">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Категория</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        @if($lead->category)
                                            {{ ucfirst($lead->category) }}
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">Не определена</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Краткое содержание</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        @if($lead->summary)
                                            {{ $lead->summary }}
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">Нет данных</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>

                            <!-- Сгенерированный ответ -->
                            @if($lead->analytics && $lead->analytics->processing_status === 'completed' && $lead->analytics->generated_response)
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Черновик ответа</h4>
                                    <div class="bg-gray-50 dark:bg-secondary-700 rounded-md p-4">
                                        <div class="prose prose-sm max-w-none dark:prose-invert markdown-content">
                                            {!! Str::of($lead->analytics->generated_response)->markdown() !!}
                                        </div>
                                    </div>
                                    <div class="mt-2 flex justify-end">
                                        <button type="button" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-primary-700 dark:text-primary-400 bg-primary-100 dark:bg-primary-900 hover:bg-primary-200 dark:hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800" onclick="copyToClipboard('{{ addslashes($lead->analytics->generated_response) }}')">
                                            <i class="fas fa-copy mr-1"></i>
                                            Копировать
                                        </button>
                                    </div>

                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-4 mb-2">Анализ заявки</h4>
                                    <div class="bg-gray-50 dark:bg-secondary-700 rounded-md p-4">
                                        <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                            <div>
                                                <dt class="text-gray-500 dark:text-gray-400">Тональность</dt>
                                                <dd class="font-medium text-gray-900 dark:text-white">
                                                    @if($lead->analytics->sentiment === 'positive')
                                                        <span class="text-green-500"><i class="fas fa-smile mr-1"></i> Позитивная</span>
                                                    @elseif($lead->analytics->sentiment === 'negative')
                                                        <span class="text-red-500"><i class="fas fa-frown mr-1"></i> Негативная</span>
                                                    @else
                                                        <span class="text-gray-500"><i class="fas fa-meh mr-1"></i> Нейтральная</span>
                                                    @endif
                                                </dd>
                                            </div>
                                            <div>
                                                <dt class="text-gray-500 dark:text-gray-400">Срочность</dt>
                                                <dd class="font-medium text-gray-900 dark:text-white">
                                                    {{ $lead->analytics->urgency_score ?? 'Не определено' }}/10
                                                </dd>
                                            </div>
                                            <div>
                                                <dt class="text-gray-500 dark:text-gray-400">Сложность</dt>
                                                <dd class="font-medium text-gray-900 dark:text-white">
                                                    {{ $lead->analytics->complexity_score ?? 'Не определено' }}/10
                                                </dd>
                                            </div>
                                        </dl>

                                        <!-- Ключевые моменты в отдельном блоке для лучшего форматирования -->
                                        <div class="mt-4 border-t border-gray-200 dark:border-secondary-600 pt-3">
                                            <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Ключевые моменты</h5>
                                            <div class="prose prose-sm max-w-none dark:prose-invert prose-gray-800 dark:prose-gray-200 markdown-content">
                                                @php
                                                    $keyPoints = null;
                                                    if (isset($lead->analytics->key_points)) {
                                                        // Если key_points уже массив
                                                        if (is_array($lead->analytics->key_points)) {
                                                            $keyPoints = $lead->analytics->key_points;
                                                        }
                                                        // Если key_points это строка в формате JSON
                                                        elseif (is_string($lead->analytics->key_points)) {
                                                            if (strpos($lead->analytics->key_points, '[') === 0) {
                                                                // Пробуем раскодировать из JSON
                                                                try {
                                                                    $decoded = json_decode($lead->analytics->key_points, true);
                                                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                                        $keyPoints = $decoded;
                                                                    } else {
                                                                        $keyPoints = [$lead->analytics->key_points];
                                                                    }
                                                                } catch (\Exception $e) {
                                                                    $keyPoints = [$lead->analytics->key_points];
                                                                }
                                                            } else {
                                                                $keyPoints = [$lead->analytics->key_points];
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                @if($keyPoints && count($keyPoints) > 0)
                                                    <ul class="pl-5 space-y-1 list-disc">
                                                        @foreach($keyPoints as $point)
                                                            <li>{!! Str::of($point)->markdown() !!}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="text-gray-500 dark:text-gray-400">Не определено</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-2 flex justify-end">
                                        <form method="POST" action="{{ route('crm.leads.generate-analytics', $lead->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-primary-700 dark:text-primary-400 bg-primary-100 dark:bg-primary-900 hover:bg-primary-200 dark:hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                                                <i class="fas fa-sync-alt mr-1"></i>
                                                Обновить анализ
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($lead->analytics && $lead->analytics->processing_status === 'processing')
                                <div class="mt-4">
                                    <div class="text-center py-4">
                                        <i class="fas fa-spinner fa-spin text-primary-600 dark:text-primary-400 text-2xl mb-2"></i>
                                        <p class="text-gray-600 dark:text-gray-300">Аналитика заявки генерируется...</p>
                                    </div>
                                </div>
                            @elseif($lead->analytics && $lead->analytics->processing_status === 'failed')
                                <div class="mt-4">
                                    <div class="text-center py-3 bg-red-50 dark:bg-red-900/20 rounded-md">
                                        <i class="fas fa-exclamation-triangle text-red-500 dark:text-red-400 text-xl mb-2"></i>
                                        <p class="text-red-600 dark:text-red-300">Не удалось сгенерировать аналитику</p>
                                        <form method="POST" action="{{ route('crm.leads.generate-analytics', $lead->id) }}" class="mt-2">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 dark:text-red-400 bg-red-100 dark:bg-red-900 hover:bg-red-200 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-secondary-800">
                                                <i class="fas fa-redo mr-1"></i>
                                                Повторить попытку
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('crm.leads.generate-analytics', $lead->id) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                                            <i class="fas fa-robot mr-2"></i>
                                            Сгенерировать ответ
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Таб "Комментарии" -->
        <div id="tab-comments" class="tab-content hidden">
            <div class="bg-white dark:bg-secondary-800 shadow-sm rounded-lg overflow-hidden">
                @livewire('lead-comments', ['leadId' => $lead->id])
            </div>
        </div>

        <!-- Таб "История изменений" -->
        <div id="tab-history" class="tab-content hidden">
            <div class="bg-white dark:bg-secondary-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        История изменений
                    </h3>
                </div>
                <div class="px-6 py-4">
                    <!-- Подтабы для истории изменений -->
                    <div class="mb-4 border-b border-gray-200 dark:border-secondary-700">
                        <nav class="-mb-px flex space-x-8" aria-label="История">
                            <button id="lead-history-tab"
                                class="whitespace-nowrap py-3 px-1 border-b-2 border-primary-500 dark:border-primary-400 text-primary-600 dark:text-primary-400 font-medium text-sm"
                                onclick="switchHistoryTab('lead')">
                                Изменения заявки
                            </button>
                            <button id="comment-history-tab"
                                class="whitespace-nowrap py-3 px-1 border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 font-medium text-sm"
                                onclick="switchHistoryTab('comment')">
                                Изменения комментариев
                            </button>
                        </nav>
                    </div>

                    <!-- Контент истории заявки -->
                    <div id="lead-history-content" class="flow-root">
                        <!-- Отладочная информация -->
                        <div class="mb-3 p-2 bg-gray-100 dark:bg-secondary-700 rounded">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Всего событий: {{ $lead->events->count() }}
                            </p>
                        </div>

                        @php
                            $leadEvents = $lead->events->filter(function ($event) {
                                // Получить события, которые не относятся к комментариям
                                if (!isset($event->metadata) || !is_array($event->metadata)) {
                                    return true; // Если metadata не массив или null, включаем в результат
                                }
                                return !isset($event->metadata['type']) || $event->metadata['type'] !== 'comment';
                            })->sortByDesc('created_at');
                        @endphp

                        @if($leadEvents->count() > 0)
                            <ul role="list" class="-mb-8">
                                @foreach($leadEvents as $event)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-secondary-700" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex items-start space-x-3">
                                                <!-- Иконка события -->
                                                <div class="relative">
                                                    <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-secondary-700 flex items-center justify-center ring-8 ring-white dark:ring-secondary-800">
                                                        @if(strpos($event->event_type, 'status') !== false)
                                                            <i class="fas fa-flag text-blue-500"></i>
                                                        @elseif(strpos($event->event_type, 'tag') !== false)
                                                            <i class="fas fa-tag text-indigo-500"></i>
                                                        @elseif(strpos($event->event_type, 'file') !== false)
                                                            <i class="fas fa-file text-green-500"></i>
                                                        @elseif($event->event_type === 'created')
                                                            <i class="fas fa-plus-circle text-green-500"></i>
                                                        @elseif($event->event_type === 'deleted')
                                                            <i class="fas fa-trash text-red-500"></i>
                                                        @elseif($event->event_type === 'restored')
                                                            <i class="fas fa-undo text-teal-500"></i>
                                                        @else
                                                            <i class="fas fa-edit text-gray-500"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                                <!-- Содержимое события -->
                                                <div class="min-w-0 flex-1">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $event->user ? $event->user->name : 'Система' }}
                                                        </div>
                                                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $event->created_at->format('d.m.Y H:i:s') }}
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                                        <p>{{ $event->description }}</p>

                                                        @if($event->previous_value || $event->new_value)
                                                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-secondary-700 pt-1">
                                                                @if($event->previous_value)
                                                                    <p>Было: <span class="font-medium">{{ $event->previous_value }}</span></p>
                                                                @endif
                                                                @if($event->new_value)
                                                                    <p>Стало: <span class="font-medium">{{ $event->new_value }}</span></p>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        @if($event->metadata && is_array($event->metadata) && count($event->metadata) > 0)
                                                            <div class="mt-1 text-xs border-t border-gray-100 dark:border-secondary-700 pt-1">
                                                                <button type="button" class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 toggle-metadata" data-event-id="{{ $event->id }}">
                                                                    Подробнее <i class="fas fa-chevron-down ml-1"></i>
                                                                </button>
                                                                <div id="metadata-{{ $event->id }}" class="hidden mt-2 bg-gray-50 dark:bg-secondary-700 p-2 rounded">
                                                                    <pre class="text-xs overflow-auto">{{ json_encode($event->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-10">
                                <i class="fas fa-history text-gray-400 text-5xl mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">История изменений заявки пуста</p>
                            </div>
                        @endif
                    </div>

                    <!-- Контент истории комментариев -->
                    <div id="comment-history-content" class="hidden flow-root">
                        <!-- Отладочная информация -->
                        <div class="mb-3 p-2 bg-gray-100 dark:bg-secondary-700 rounded">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Всего событий комментариев: {{ $lead->events->where('metadata->type', 'comment')->count() }}
                            </p>
                        </div>

                        @php
                            $commentEvents = $lead->events->filter(function ($event) {
                                // Получить только события, связанные с комментариями
                                if (!isset($event->metadata) || !is_array($event->metadata)) {
                                    return false;
                                }
                                return isset($event->metadata['type']) && $event->metadata['type'] === 'comment';
                            })->sortByDesc('created_at');
                        @endphp

                        @if($commentEvents->count() > 0)
                            <ul role="list" class="-mb-8">
                                @foreach($commentEvents as $event)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-secondary-700" aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex items-start space-x-3">
                                                <!-- Иконка события -->
                                                <div class="relative">
                                                    <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-secondary-700 flex items-center justify-center ring-8 ring-white dark:ring-secondary-800">
                                                        @if(strpos($event->event_type, 'comment_created') !== false)
                                                            <i class="fas fa-comment-dots text-green-500"></i>
                                                        @elseif(strpos($event->event_type, 'comment_updated') !== false)
                                                            <i class="fas fa-comment-edit text-blue-500"></i>
                                                        @elseif(strpos($event->event_type, 'comment_deleted') !== false)
                                                            <i class="fas fa-comment-slash text-red-500"></i>
                                                        @elseif(strpos($event->event_type, 'comment_restored') !== false)
                                                            <i class="fas fa-comment-medical text-teal-500"></i>
                                                        @else
                                                            <i class="fas fa-comment text-yellow-500"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                                <!-- Содержимое события -->
                                                <div class="min-w-0 flex-1">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $event->user ? $event->user->name : 'Система' }}
                                                        </div>
                                                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $event->created_at->format('d.m.Y H:i:s') }}
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                                        <p>{{ $event->description }}</p>

                                                        @if($event->previous_value || $event->new_value)
                                                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-secondary-700 pt-1">
                                                                @if($event->previous_value)
                                                                    <p>Было: <span class="font-medium">{{ $event->previous_value }}</span></p>
                                                                @endif
                                                                @if($event->new_value)
                                                                    <p>Стало: <span class="font-medium">{{ $event->new_value }}</span></p>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        @if($event->metadata && is_array($event->metadata) && count($event->metadata) > 0)
                                                            <div class="mt-1 text-xs border-t border-gray-100 dark:border-secondary-700 pt-1">
                                                                <button type="button" class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 toggle-metadata" data-event-id="{{ $event->id }}">
                                                                    Подробнее <i class="fas fa-chevron-down ml-1"></i>
                                                                </button>
                                                                <div id="metadata-{{ $event->id }}" class="hidden mt-2 bg-gray-50 dark:bg-secondary-700 p-2 rounded">
                                                                    <pre class="text-xs overflow-auto">{{ json_encode($event->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-10">
                                <i class="fas fa-history text-gray-400 text-5xl mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400">История изменений комментариев пуста</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Таб "Редактирование" -->
        <div id="tab-edit" class="tab-content hidden">
            <div class="bg-white dark:bg-secondary-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Редактирование заявки
                    </h3>
                </div>
                <div class="px-6 py-4">
                    <form method="POST" action="{{ route('crm.leads.update', $lead->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Основная информация о контакте -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Имя</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $lead->name) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="source" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Источник</label>
                                <input type="text" name="source" id="source" value="{{ old('source', $lead->source) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                @error('source')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $lead->email) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Телефон</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $lead->phone) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Сообщение клиента -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Сообщение</label>
                            <textarea name="message" id="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('message', $lead->message) }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Дополнительные поля -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Категория</label>
                                <input type="text" name="category" id="category" value="{{ old('category', $lead->category) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="relevance_score" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Оценка релевантности (1-10)</label>
                                <input type="number" name="relevance_score" id="relevance_score" min="1" max="10" value="{{ old('relevance_score', $lead->relevance_score) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                @error('relevance_score')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Теги -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Теги</label>
                            <div class="flex flex-wrap gap-2 mb-2">
                                @foreach($availableTags as $tag)
                                    <label class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-secondary-600 rounded-full text-sm font-medium bg-white dark:bg-secondary-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-600 transition-colors">
                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="sr-only peer"
                                            {{ (in_array($tag->id, old('tags', $lead->tags->pluck('id')->toArray())) ? 'checked' : '') }}>
                                        <span class="peer-checked:bg-primary-100 peer-checked:text-primary-800 dark:peer-checked:bg-primary-900 dark:peer-checked:text-primary-300 px-2 py-1 rounded-full transition-colors">
                                            {{ $tag->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="flex items-center mt-3">
                                <label for="new_tag" class="sr-only">Новый тег</label>
                                <input type="text" id="new_tag" placeholder="Добавить новый тег" class="block w-full rounded-l-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <button type="button" id="add_tag_btn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                                    <i class="fas fa-plus mr-1"></i>
                                    Добавить
                                </button>
                            </div>

                            @error('tags')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Кнопки действий -->
                        <div class="flex justify-end space-x-3 border-t border-gray-200 dark:border-secondary-700 pt-6">
                            <button type="button" onclick="document.getElementById('tab-details').click();" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-secondary-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-secondary-700 hover:bg-gray-50 dark:hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                                Отмена
                            </button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                                <i class="fas fa-save mr-2"></i>
                                Сохранить изменения
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Переключение основных табов
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-content');

            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Удалить активный класс у всех ссылок и скрыть все табы
                    tabLinks.forEach(l => l.classList.remove('active', 'border-primary-500', 'dark:border-primary-400', 'text-primary-600', 'dark:text-primary-400'));
                    tabLinks.forEach(l => l.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400'));
                    tabContents.forEach(c => c.classList.add('hidden'));

                    // Активировать текущую ссылку и таб
                    this.classList.add('active', 'border-primary-500', 'dark:border-primary-400', 'text-primary-600', 'dark:text-primary-400');
                    this.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');

                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.remove('hidden');
                });
            });

            // Показать/скрыть метаданные события
            document.querySelectorAll('.toggle-metadata').forEach(button => {
                button.addEventListener('click', function() {
                    const eventId = this.getAttribute('data-event-id');
                    const metadataDiv = document.getElementById('metadata-' + eventId);

                    if (metadataDiv.classList.contains('hidden')) {
                        metadataDiv.classList.remove('hidden');
                        this.querySelector('i').classList.remove('fa-chevron-down');
                        this.querySelector('i').classList.add('fa-chevron-up');
                    } else {
                        metadataDiv.classList.add('hidden');
                        this.querySelector('i').classList.remove('fa-chevron-up');
                        this.querySelector('i').classList.add('fa-chevron-down');
                    }
                });
            });

            // Добавление нового тега
            const addTagBtn = document.getElementById('add_tag_btn');
            const newTagInput = document.getElementById('new_tag');

            if (addTagBtn && newTagInput) {
                addTagBtn.addEventListener('click', function() {
                    const tagName = newTagInput.value.trim();
                    if (tagName) {
                        // Отправка AJAX-запроса для создания тега
                        fetch('{{ route('crm.tags.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ name: tagName })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Ошибка сети');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Добавление нового тега в форму
                                const tagsContainer = document.querySelector('.flex.flex-wrap.gap-2.mb-2');

                                const tagLabel = document.createElement('label');
                                tagLabel.className = 'inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-secondary-600 rounded-full text-sm font-medium bg-white dark:bg-secondary-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-600 transition-colors';

                                const tagInput = document.createElement('input');
                                tagInput.type = 'checkbox';
                                tagInput.name = 'tags[]';
                                tagInput.value = data.tag.id;
                                tagInput.className = 'sr-only peer';
                                tagInput.checked = true;

                                const tagSpan = document.createElement('span');
                                tagSpan.className = 'peer-checked:bg-primary-100 peer-checked:text-primary-800 dark:peer-checked:bg-primary-900 dark:peer-checked:text-primary-300 px-2 py-1 rounded-full transition-colors';
                                tagSpan.textContent = data.tag.name;

                                tagLabel.appendChild(tagInput);
                                tagLabel.appendChild(tagSpan);
                                tagsContainer.appendChild(tagLabel);

                                // Очистка поля ввода
                                newTagInput.value = '';
                            } else {
                                alert('Ошибка при создании тега: ' + (data.message || 'Неизвестная ошибка'));
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка:', error);
                            alert('Ошибка при создании тега');
                        });
                    }
                });

                // Добавление тега по нажатию Enter
                newTagInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addTagBtn.click();
                    }
                });
            }
        });

        // Функция для переключения между подтабами в истории изменений
        function switchHistoryTab(tabType) {
            // Переключаем классы для кнопок подтабов
            const leadTab = document.getElementById('lead-history-tab');
            const commentTab = document.getElementById('comment-history-tab');

            if (tabType === 'lead') {
                leadTab.classList.add('border-primary-500', 'dark:border-primary-400', 'text-primary-600', 'dark:text-primary-400');
                leadTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');

                commentTab.classList.remove('border-primary-500', 'dark:border-primary-400', 'text-primary-600', 'dark:text-primary-400');
                commentTab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');

                // Показываем контент истории заявки и скрываем контент истории комментариев
                document.getElementById('lead-history-content').classList.remove('hidden');
                document.getElementById('comment-history-content').classList.add('hidden');
            } else {
                commentTab.classList.add('border-primary-500', 'dark:border-primary-400', 'text-primary-600', 'dark:text-primary-400');
                commentTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');

                leadTab.classList.remove('border-primary-500', 'dark:border-primary-400', 'text-primary-600', 'dark:text-primary-400');
                leadTab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');

                // Показываем контент истории комментариев и скрываем контент истории заявки
                document.getElementById('comment-history-content').classList.remove('hidden');
                document.getElementById('lead-history-content').classList.add('hidden');
            }
        }

        // Функция копирования в буфер обмена
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Ответ скопирован в буфер обмена');
            }, function(err) {
                console.error('Ошибка при копировании текста: ', err);
            });
        }
    </script>
    @endpush

    @push('styles')
    <style>
        /* Стили для Markdown-контента */
        .markdown-content ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .markdown-content ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .markdown-content h1,
        .markdown-content h2,
        .markdown-content h3,
        .markdown-content h4,
        .markdown-content h5,
        .markdown-content h6 {
            font-weight: 600;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .markdown-content p {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .markdown-content a {
            color: #2563eb;
            text-decoration: underline;
        }

        .dark .markdown-content a {
            color: #3b82f6;
        }

        .markdown-content blockquote {
            border-left: 4px solid #e5e7eb;
            padding-left: 1rem;
            font-style: italic;
            margin: 0.5rem 0;
        }

        .dark .markdown-content blockquote {
            border-left-color: #4b5563;
        }

        .markdown-content code {
            font-family: monospace;
            background-color: #f3f4f6;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.875em;
        }

        .dark .markdown-content code {
            background-color: #374151;
        }

        .markdown-content pre {
            background-color: #f3f4f6;
            padding: 1rem;
            border-radius: 0.375rem;
            overflow-x: auto;
            margin: 0.75rem 0;
        }

        .dark .markdown-content pre {
            background-color: #1f2937;
        }

        .markdown-content pre code {
            background-color: transparent;
            padding: 0;
        }
    </style>
    @endpush
@endsection
