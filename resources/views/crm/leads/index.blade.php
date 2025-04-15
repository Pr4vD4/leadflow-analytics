@extends('layouts.crm')

@section('title', 'Управление заявками')

@section('content')
    <div class="container mx-auto">
        <!-- Заголовок страницы -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Заявки</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Управление и аналитика клиентских заявок</p>
        </div>

        <!-- Фильтры и поиск -->
        <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('crm.leads.index') }}" class="space-y-4">
                <div class="flex flex-wrap gap-4">
                    <!-- Поиск -->
                    <div class="flex-grow min-w-[200px]">
                        <label for="filter[search]" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Поиск</label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="text" name="filter[search]" id="filter[search]" value="{{ request('filter.search') }}" class="block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Имя, email, телефон или сообщение...">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Статус -->
                    <div class="w-full sm:w-auto">
                        <label for="filter[status]" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Статус</label>
                        <select id="filter[status]" name="filter[status]" class="block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">Все статусы</option>
                            <option value="new" {{ request('filter.status') == 'new' ? 'selected' : '' }}>Новая</option>
                            <option value="in_progress" {{ request('filter.status') == 'in_progress' ? 'selected' : '' }}>В работе</option>
                            <option value="completed" {{ request('filter.status') == 'completed' ? 'selected' : '' }}>Завершена</option>
                            <option value="archived" {{ request('filter.status') == 'archived' ? 'selected' : '' }}>В архиве</option>
                        </select>
                    </div>

                    <!-- Источник -->
                    <div class="w-full sm:w-auto">
                        <label for="filter[source]" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Источник</label>
                        <select id="filter[source]" name="filter[source]" class="block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">Все источники</option>
                            @foreach($sources as $source)
                                <option value="{{ $source }}" {{ request('filter.source') == $source ? 'selected' : '' }}>{{ $source }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                        <i class="fas fa-filter mr-2"></i>
                        Применить фильтры
                    </button>

                    <!-- Кнопка сброса фильтров -->
                    @if(request('filter'))
                        <a href="{{ route('crm.leads.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-secondary-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-secondary-700 hover:bg-gray-50 dark:hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                            <i class="fas fa-times mr-2"></i>
                            Сбросить фильтры
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Таблица заявок -->
        <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                @if(count($leads) > 0)
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-secondary-700">
                        <thead class="bg-gray-50 dark:bg-secondary-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Источник</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Контакт</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Сообщение</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Релевантность</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Дата</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-secondary-800 divide-y divide-gray-200 dark:divide-secondary-700">
                            @foreach($leads as $lead)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        #{{ $lead->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $lead->source }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $lead->name ?: 'Не указано' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $lead->email ?: $lead->phone ?: 'Нет контактов' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white line-clamp-2">
                                            {{ $lead->message ?: 'Нет сообщения' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        @if($lead->relevance_score)
                                            <span class="inline-flex items-center">
                                                {{ $lead->relevance_score }}/10
                                                <i class="fas fa-star text-yellow-400 ml-1"></i>
                                            </span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $lead->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('crm.leads.show', $lead->id) }}" class="text-primary-600 dark:text-primary-500 hover:text-primary-900 dark:hover:text-primary-400">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-10">
                        <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">Заявки не найдены</p>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Попробуйте изменить параметры фильтрации или добавить новые заявки</p>

                        @if(request('filter'))
                            <a href="{{ route('crm.leads.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-secondary-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-secondary-700 hover:bg-gray-50 dark:hover:bg-secondary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                                <i class="fas fa-times mr-2"></i>
                                Сбросить фильтры
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Пагинация -->
            @if($leads->hasPages())
                <div class="px-6 py-4 bg-white dark:bg-secondary-800 border-t border-gray-200 dark:border-secondary-700">
                    {{ $leads->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
