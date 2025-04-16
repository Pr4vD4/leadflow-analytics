@extends('layouts.crm')

@section('title', 'Аналитика заявок')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Аналитика заявок</h1>

            <!-- Фильтры -->
            <div class="bg-white dark:bg-secondary-800 shadow rounded-lg mt-4 p-5">
                <h2 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Фильтры</h2>

                <form action="{{ route('crm.analytics.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Период -->
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">С даты</label>
                            <input type="date" id="date_from" name="date_from"
                                   value="{{ request('date_from') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-secondary-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>

                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">По дату</label>
                            <input type="date" id="date_to" name="date_to"
                                   value="{{ request('date_to') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-secondary-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>

                        <!-- Статус -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Статус</label>
                            <select id="status" name="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-secondary-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Все статусы</option>
                                @foreach($filterOptions['statuses'] as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Источник -->
                        <div>
                            <label for="source" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Источник</label>
                            <select id="source" name="source"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-secondary-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Все источники</option>
                                @foreach($filterOptions['sources'] as $source)
                                    <option value="{{ $source }}" {{ request('source') == $source ? 'selected' : '' }}>
                                        {{ $source }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Тег -->
                        <div>
                            <label for="tag" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Тег</label>
                            <select id="tag" name="tag"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-secondary-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Все теги</option>
                                @foreach($filterOptions['tags'] as $id => $name)
                                    <option value="{{ $id }}" {{ request('tag') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Группировка данных для графика -->
                        <div>
                            <label for="group_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Группировка по времени</label>
                            <select id="group_by" name="group_by"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-secondary-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                @foreach($filterOptions['group_by'] as $value => $label)
                                    <option value="{{ $value }}" {{ (request('group_by', 'day')) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                            Применить фильтры
                        </button>

                        <a href="{{ route('crm.analytics.export-csv', request()->all()) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Экспорт CSV
                        </a>
                    </div>
                </form>
            </div>

            <!-- Статистика верхнего уровня -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Общее количество заявок -->
                <div class="bg-white dark:bg-secondary-800 shadow rounded-lg p-5">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Всего заявок</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $statistics['total_leads'] }}</p>
                </div>

                <!-- Конверсия -->
                <div class="bg-white dark:bg-secondary-800 shadow rounded-lg p-5">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Конверсия</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $statistics['conversion_rate'] }}%</p>
                </div>

                <!-- Средняя оценка релевантности -->
                <div class="bg-white dark:bg-secondary-800 shadow rounded-lg p-5">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Средняя релевантность</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $statistics['relevance_score_avg'] }}/10</p>
                </div>

                <!-- Самый популярный источник -->
                <div class="bg-white dark:bg-secondary-800 shadow rounded-lg p-5">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Популярный источник</h3>
                    @php
                        $topSource = !empty($statistics['source_distribution'])
                            ? array_key_first($statistics['source_distribution'])
                            : 'Нет данных';
                    @endphp
                    <p class="mt-2 text-xl font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $topSource }}</p>
                </div>
            </div>

            <!-- Графики и таблицы -->
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- График динамики заявок -->
                <div class="bg-white dark:bg-secondary-800 shadow rounded-lg p-5">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Динамика заявок</h3>
                    <div style="height: 300px;">
                        <canvas id="leadsChart"></canvas>
                    </div>
                </div>

                <!-- Распределение по статусам -->
                <div class="bg-white dark:bg-secondary-800 shadow rounded-lg p-5">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">По статусам</h3>
                    <div style="height: 300px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Распределение по источникам -->
                <div class="bg-white dark:bg-secondary-800 shadow rounded-lg p-5">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">По источникам</h3>
                    <div style="height: 300px;">
                        <canvas id="sourceChart"></canvas>
                    </div>
                </div>

                <!-- Таблица с данными по статусам -->
                <div class="bg-white dark:bg-secondary-800 shadow rounded-lg p-5">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Детализация по статусам</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-secondary-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Статус
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-secondary-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Количество
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-secondary-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Процент
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-secondary-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($statistics['leads_grouped'] as $status => $count)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $status }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @if($statistics['total_leads'] > 0)
                                                {{ round(($count / $statistics['total_leads']) * 100, 1) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Динамика заявок
        const leadsChartCtx = document.getElementById('leadsChart').getContext('2d');
        const chartLabels = {!! json_encode($statistics['chart_data']['labels'] ?? []) !!};
        const chartData = {!! json_encode($statistics['chart_data']['data'] ?? []) !!};

        new Chart(leadsChartCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Количество заявок',
                    data: chartData,
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Распределение по статусам
        const statusChartCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusChartCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys({!! json_encode($statistics['leads_grouped']) !!}),
                datasets: [{
                    data: Object.values({!! json_encode($statistics['leads_grouped']) !!}),
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.7)',
                        'rgba(251, 146, 60, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(107, 114, 128, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        // Распределение по источникам
        const sourceChartCtx = document.getElementById('sourceChart').getContext('2d');
        const sourceLabels = Object.keys({!! json_encode($statistics['source_distribution']) !!});
        const sourceData = Object.values({!! json_encode($statistics['source_distribution']) !!});

        new Chart(sourceChartCtx, {
            type: 'bar',
            data: {
                labels: sourceLabels,
                datasets: [{
                    label: 'Количество заявок',
                    data: sourceData,
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
