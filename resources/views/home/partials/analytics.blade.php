<!-- Блок аналитики -->
<section id="analytics" class="py-24 bg-secondary-50 dark:bg-secondary-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl font-bold font-heading text-secondary-900 dark:text-white">
                Аналитика, которая работает на вас
            </h2>
            <p class="mt-4 text-xl text-secondary-600 dark:text-secondary-300 max-w-3xl mx-auto">
                Превратите ваши данные в действенные инсайты и стратегические решения
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white dark:bg-secondary-800 rounded-xl shadow-lg p-6" data-aos="zoom-in">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- График конверсии -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-secondary-700 dark:text-secondary-300">Конверсия лидов</h4>
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="conversionRate">--</span>
                        </div>
                        <div style="height: 120px;">
                            <canvas id="conversionChart"></canvas>
                        </div>
                    </div>

                    <!-- Круговая диаграмма источников -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-4">Источники лидов</h4>
                        <div style="height: 150px;">
                            <canvas id="sourcesChart"></canvas>
                        </div>
                    </div>

                    <!-- График активности по дням -->
                    <div class="md:col-span-2 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-secondary-700 dark:text-secondary-300 mb-4">Активность лидов за месяц</h4>
                        <div style="height: 200px;">
                            <canvas id="activityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-secondary-50 dark:bg-secondary-800 rounded-xl p-6 shadow-sm border border-secondary-100 dark:border-secondary-700" data-aos="fade-left" data-aos-delay="100">
                    <h3 class="text-lg font-bold font-heading text-secondary-900 dark:text-white">Конверсия лидов</h3>
                    <p class="mt-2 text-secondary-600 dark:text-secondary-300">
                        Отслеживайте путь клиента от первого контакта до сделки и оптимизируйте процесс.
                    </p>
                </div>

                <div class="bg-secondary-50 dark:bg-secondary-800 rounded-xl p-6 shadow-sm border border-secondary-100 dark:border-secondary-700" data-aos="fade-left" data-aos-delay="200">
                    <h3 class="text-lg font-bold font-heading text-secondary-900 dark:text-white">Прогнозирование</h3>
                    <p class="mt-2 text-secondary-600 dark:text-secondary-300">
                        Используйте AI для предсказания поведения клиентов и планирования будущих кампаний.
                    </p>
                </div>

                <div class="bg-secondary-50 dark:bg-secondary-800 rounded-xl p-6 shadow-sm border border-secondary-100 dark:border-secondary-700" data-aos="fade-left" data-aos-delay="300">
                    <h3 class="text-lg font-bold font-heading text-secondary-900 dark:text-white">Отчеты и дашборды</h3>
                    <p class="mt-2 text-secondary-600 dark:text-secondary-300">
                        Создавайте настраиваемые отчеты и получайте автоматические уведомления о ключевых метриках.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Подключение Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функция генерации случайных данных
    function generateRandomData(count, min = 0, max = 100) {
        return Array.from({length: count}, () => Math.floor(Math.random() * (max - min + 1)) + min);
    }

    function generateRandomLabels(count, prefix = 'День') {
        return Array.from({length: count}, (_, i) => `${prefix} ${i + 1}`);
    }

    // Настройки темы
    const isDark = document.documentElement.classList.contains('dark') ||
                   document.body.classList.contains('dark') ||
                   window.matchMedia('(prefers-color-scheme: dark)').matches;
    const textColor = isDark ? '#E5E7EB' : '#374151';
    const gridColor = isDark ? '#374151' : '#E5E7EB';

    // 1. График конверсии лидов (линейный)
    const conversionCtx = document.getElementById('conversionChart');
    if (conversionCtx) {
        const conversionData = generateRandomData(7, 60, 90);
        const avgConversion = Math.round(conversionData.reduce((a, b) => a + b, 0) / conversionData.length);
        document.getElementById('conversionRate').textContent = avgConversion + '%';

        new Chart(conversionCtx, {
            type: 'line',
            data: {
                labels: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
                datasets: [{
                    label: 'Конверсия %',
                    data: conversionData,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#3B82F6',
                    pointBorderWidth: 2,
                    pointBorderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: textColor,
                            font: { size: 11 }
                        }
                    },
                    y: {
                        beginAtZero: false,
                        min: 50,
                        max: 100,
                        grid: {
                            color: gridColor + '40',
                            drawBorder: false
                        },
                        ticks: {
                            color: textColor,
                            font: { size: 10 },
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Круговая диаграмма источников
    const sourcesCtx = document.getElementById('sourcesChart');
    if (sourcesCtx) {
        const sourcesData = generateRandomData(4, 10, 40);
        const total = sourcesData.reduce((a, b) => a + b, 0);
        const percentages = sourcesData.map(val => Math.round((val / total) * 100));

        new Chart(sourcesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Сайт', 'Соц.сети', 'Email', 'Реклама'],
                datasets: [{
                    data: sourcesData,
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444'
                    ],
                    borderWidth: 0,
                    cutout: '50%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            color: textColor,
                            font: {
                                size: 10,
                                family: 'Arial, sans-serif'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 10,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                const isDarkTheme = document.documentElement.classList.contains('dark') ||
                                                  document.body.classList.contains('dark') ||
                                                  window.matchMedia('(prefers-color-scheme: dark)').matches;
                                const labelColor = isDarkTheme ? '#E5E7EB' : '#374151';
                                return data.labels.map((label, i) => ({
                                    text: `${label} ${percentages[i]}%`,
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    strokeStyle: data.datasets[0].backgroundColor[i],
                                    pointStyle: 'circle',
                                    fontColor: labelColor
                                }));
                            }
                        }
                    }
                },
                // Дополнительная настройка для темной темы
                color: textColor
            }
        });
    }

    // 3. График активности (столбчатый)
    const activityCtx = document.getElementById('activityChart');
    if (activityCtx) {
        const days = 30;
        const newLeads = generateRandomData(days, 5, 25);
        const convertedLeads = newLeads.map(val => Math.floor(val * (Math.random() * 0.4 + 0.3))); // 30-70% от новых

        new Chart(activityCtx, {
            type: 'bar',
            data: {
                labels: generateRandomLabels(days),
                datasets: [
                    {
                        label: 'Новые лиды',
                        data: newLeads,
                        backgroundColor: 'rgba(139, 92, 246, 0.8)',
                        borderRadius: 3,
                        borderSkipped: false
                    },
                    {
                        label: 'Конвертированные',
                        data: convertedLeads,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 3,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: textColor,
                            font: { size: 11 },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 15
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: textColor,
                            font: { size: 9 },
                            maxTicksLimit: 10
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: gridColor + '40',
                            drawBorder: false
                        },
                        ticks: {
                            color: textColor,
                            font: { size: 10 }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
});
</script>
