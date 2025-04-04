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
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">Не оценено</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Теги</dt>
                                <dd class="mt-1">
                                    @if($lead->tags->count() > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($lead->tags as $tag)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300">
                                                    {{ $tag->name }}
                                                </span>
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
                        @if($lead->generated_response)
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Черновик ответа</h4>
                                <div class="bg-gray-50 dark:bg-secondary-700 rounded-md p-4">
                                    <p class="text-sm text-gray-900 dark:text-white">{{ $lead->generated_response }}</p>
                                </div>
                                <div class="mt-2 flex justify-end">
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-primary-700 dark:text-primary-400 bg-primary-100 dark:bg-primary-900 hover:bg-primary-200 dark:hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                                        <i class="fas fa-copy mr-1"></i>
                                        Копировать
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="mt-4">
                                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                                    <i class="fas fa-robot mr-2"></i>
                                    Сгенерировать ответ
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
