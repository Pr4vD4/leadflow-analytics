@extends('layouts.crm')

@section('title', 'Дашборд')

@section('content')
    <div class="container mx-auto">
        <!-- Заголовок страницы -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Дашборд</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Основные показатели и статистика по заявкам</p>
        </div>

        <!-- Фильтры -->
        <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-3">
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Период</label>
                        <select id="period" class="block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="today">Сегодня</option>
                            <option value="yesterday">Вчера</option>
                            <option value="week" selected>Неделя</option>
                            <option value="month">Месяц</option>
                            <option value="quarter">Квартал</option>
                            <option value="year">Год</option>
                        </select>
                    </div>
                    <div>
                        <label for="source" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Источник</label>
                        <select id="source" class="block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="all" selected>Все источники</option>
                            <option value="website">Сайт</option>
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                        </select>
                    </div>
                </div>
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                    <i class="fas fa-file-csv mr-2"></i>
                    Экспорт CSV
                </button>
            </div>
        </div>

        <!-- Ключевые метрики -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Общее количество заявок -->
            <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Всего заявок</h3>
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <i class="fas fa-clipboard-list text-blue-500 dark:text-blue-400"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">248</div>
                    <p class="text-sm text-green-600 dark:text-green-500 flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        12% с прошлой недели
                    </p>
                </div>
            </div>

            <!-- Конверсия -->
            <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Конверсия</h3>
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-full">
                        <i class="fas fa-percent text-green-500 dark:text-green-400"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">68%</div>
                    <p class="text-sm text-green-600 dark:text-green-500 flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        5% с прошлой недели
                    </p>
                </div>
            </div>

            <!-- Средняя оценка релевантности -->
            <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Средняя релевантность</h3>
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-full">
                        <i class="fas fa-star-half-alt text-purple-500 dark:text-purple-400"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">7.8</div>
                    <p class="text-sm text-red-600 dark:text-red-500 flex items-center mt-1">
                        <i class="fas fa-arrow-down mr-1"></i>
                        0.3 с прошлой недели
                    </p>
                </div>
            </div>

            <!-- Среднее время обработки -->
            <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Среднее время обработки</h3>
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                        <i class="fas fa-clock text-yellow-500 dark:text-yellow-400"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">4.2 ч</div>
                    <p class="text-sm text-green-600 dark:text-green-500 flex items-center mt-1">
                        <i class="fas fa-arrow-down mr-1"></i>
                        0.8 ч с прошлой недели
                    </p>
                </div>
            </div>
        </div>

        <!-- Графики -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Динамика заявок и конверсии -->
            <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4">
                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4">Динамика заявок и конверсии</h3>
                <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-secondary-700 rounded-lg">
                    <div class="text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-chart-line text-4xl mb-3"></i>
                        <p>Здесь будет график</p>
                    </div>
                </div>
            </div>

            <!-- Распределение по источникам -->
            <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4">
                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4">Распределение по источникам</h3>
                <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-secondary-700 rounded-lg">
                    <div class="text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-chart-pie text-4xl mb-3"></i>
                        <p>Здесь будет диаграмма</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Последние заявки -->
        <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-medium text-gray-900 dark:text-white">Последние заявки</h3>
                <a href="#" class="text-sm text-primary-600 dark:text-primary-500 hover:text-primary-700 dark:hover:text-primary-400">
                    Все заявки
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-secondary-700">
                    <thead class="bg-gray-50 dark:bg-secondary-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Источник</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Контакт</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Релевантность</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Дата</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-secondary-800 divide-y divide-gray-200 dark:divide-secondary-700">
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">#123</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Сайт</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">+7 (999) 123-45-67</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Завершена
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">8.5</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">2 часа назад</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">#122</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Facebook</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">mail@example.com</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    В работе
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">7.2</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">5 часов назад</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">#121</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Instagram</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">+7 (888) 987-65-43</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Новая
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">6.9</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">8 часов назад</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Здесь будет инициализация графиков, когда они будут добавлены
    });
</script>
@endpush
