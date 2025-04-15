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
        <form method="GET" action="{{ route('crm.dashboard') }}" class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-3">
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Период</label>
                        <select id="period" name="period" class="block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" onchange="this.form.submit()">
                            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Сегодня</option>
                            <option value="yesterday" {{ $period === 'yesterday' ? 'selected' : '' }}>Вчера</option>
                            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Неделя</option>
                            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Месяц</option>
                            <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>Квартал</option>
                            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Год</option>
                        </select>
                    </div>
                    <div>
                        <label for="source" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Источник</label>
                        <select id="source" name="source" class="block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" onchange="this.form.submit()">
                            <option value="all" {{ $source === 'all' ? 'selected' : '' }}>Все источники</option>
                            @if($metrics['source_distribution'])
                                @foreach($metrics['source_distribution'] as $sourceKey => $count)
                                    <option value="{{ $sourceKey }}" {{ $source === $sourceKey ? 'selected' : '' }}>{{ $sourceKey }}</option>
                                @endforeach
                            @else
                                <option value="website" {{ $source === 'website' ? 'selected' : '' }}>Сайт</option>
                                <option value="facebook" {{ $source === 'facebook' ? 'selected' : '' }}>Facebook</option>
                                <option value="instagram" {{ $source === 'instagram' ? 'selected' : '' }}>Instagram</option>
                            @endif
                        </select>
                    </div>
                </div>
                <a href="{{ route('crm.dashboard.export-csv', ['period' => $period, 'source' => $source]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                    <i class="fas fa-file-csv mr-2"></i>
                    Экспорт CSV
                </a>
            </div>
        </form>

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
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $metrics['total_leads'] ?? 0 }}</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center mt-1">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        {{ ucfirst($period) }}
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
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['conversion_rate'] ?? 0, 1) }}%</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center mt-1">
                        <i class="fas fa-check-circle mr-1"></i>
                        Завершенные заявки
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
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['avg_relevance_score'] ?? 0, 1) }}</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center mt-1">
                        <i class="fas fa-balance-scale mr-1"></i>
                        Из 10 баллов
                    </p>
                </div>
            </div>

            <!-- Среднее время ответа -->
            <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Среднее время ответа</h3>
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                        <i class="fas fa-clock text-yellow-500 dark:text-yellow-400"></i>
                    </div>
                </div>
                <div class="mt-3">
                    @php
                        $avgResponseTime = $metrics['avg_response_time'] ?? 0;
                        $responseTimeFormatted = $avgResponseTime > 60
                            ? number_format($avgResponseTime / 60, 1) . ' ч'
                            : number_format($avgResponseTime, 0) . ' мин';
                    @endphp
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $responseTimeFormatted }}</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center mt-1">
                        <i class="fas fa-hourglass-half mr-1"></i>
                        Время до первого ответа
                    </p>
                </div>
            </div>
        </div>

        <!-- Графики -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Динамика заявок и конверсии -->
            <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4">
                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4">Динамика заявок и конверсии</h3>
                <div class="h-64">
                    <canvas id="leadsTrendChart"></canvas>
                </div>
            </div>

            <!-- Распределение по источникам -->
            <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4">
                <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4">Распределение по источникам</h3>
                <div class="h-64">
                    <canvas id="sourcesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Последние заявки -->
        <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm p-4 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-medium text-gray-900 dark:text-white">Последние заявки</h3>
                <a href="{{ route('crm.leads.index') }}" class="text-sm text-primary-600 dark:text-primary-500 hover:text-primary-700 dark:hover:text-primary-400">
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
                        @forelse($recentLeads as $lead)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <a href="{{ route('crm.leads.show', $lead->id) }}" class="hover:text-primary-600 dark:hover:text-primary-400">
                                        #{{ $lead->id }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $lead->source }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $lead->phone ?: $lead->email }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'new' => 'blue',
                                            'in_progress' => 'yellow',
                                            'completed' => 'green',
                                            'archived' => 'gray'
                                        ];
                                        $color = $statusColors[$lead->status] ?? 'gray';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-800 dark:bg-{{ $color }}-900 dark:text-{{ $color }}-200">
                                        {{ $lead->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $lead->relevance_score }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $lead->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Нет данных для отображения
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Определение цветов для темной и светлой темы
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#e5e7eb' : '#374151';
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';

        // График динамики заявок
        const trendsCtx = document.getElementById('leadsTrendChart').getContext('2d');
        const leadsTrendChart = new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($leadsTrend['dates'] ?? []) !!},
                datasets: [
                    {
                        label: 'Всего заявок',
                        data: {!! json_encode($leadsTrend['counts'] ?? []) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3
                    },
                    {
                        label: 'Завершенные',
                        data: {!! json_encode($leadsTrend['completed'] ?? []) !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: textColor,
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: textColor
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: textColor
                        }
                    }
                }
            }
        });

        // График распределения по источникам
        const sourcesData = {!! json_encode($metrics['source_distribution'] ?? []) !!};
        if (Object.keys(sourcesData).length > 0) {
            const sourcesCtx = document.getElementById('sourcesChart').getContext('2d');
            const sourcesChart = new Chart(sourcesCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(sourcesData),
                    datasets: [{
                        data: Object.values(sourcesData),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(249, 115, 22, 0.7)',
                            'rgba(139, 92, 246, 0.7)',
                            'rgba(236, 72, 153, 0.7)',
                            'rgba(245, 158, 11, 0.7)'
                        ],
                        borderColor: isDarkMode ? 'rgba(30, 41, 59, 1)' : 'white',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: textColor,
                                padding: 15
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('sourcesChart').parentNode.innerHTML = `
                <div class="h-64 flex items-center justify-center">
                    <div class="text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-chart-pie text-4xl mb-3"></i>
                        <p>Нет данных для отображения</p>
                    </div>
                </div>
            `;
        }
    });
</script>
@endpush
