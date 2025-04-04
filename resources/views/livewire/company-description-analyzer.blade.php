<div class="description-analyzer-component">
    <!-- Индикатор анализа -->
    @if($isAnalyzing)
    <div class="text-center py-3">
        <div class="inline-block animate-spin rounded-full h-5 w-5 border-t-2 border-b-2 border-primary-color"></div>
        <span class="ml-2 text-sm text-gray-500">Анализируем описание...</span>
    </div>
    @endif

    <!-- Результаты анализа -->
    @if($hasAnalyzed && !empty($analysisResults) && isset($analysisResults['score']))
    <div class="analysis-results mt-4 p-4 rounded-lg"
        x-data="{ showDetails: false }"
        :class="{
            'bg-red-50 dark:bg-red-950/30': {{ $analysisResults['score'] }} < 4,
            'bg-yellow-50 dark:bg-yellow-950/30': {{ $analysisResults['score'] }} >= 4 && {{ $analysisResults['score'] }} < 7,
            'bg-green-50 dark:bg-green-950/30': {{ $analysisResults['score'] }} >= 7
        }">

        <!-- Оценка с визуальным индикатором -->
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center">
                <div class="relative w-12 h-12 flex items-center justify-center rounded-full mr-3"
                    :class="{
                        'bg-red-100 dark:bg-red-900/50': {{ $analysisResults['score'] }} < 4,
                        'bg-yellow-100 dark:bg-yellow-900/50': {{ $analysisResults['score'] }} >= 4 && {{ $analysisResults['score'] }} < 7,
                        'bg-green-100 dark:bg-green-900/50': {{ $analysisResults['score'] }} >= 7
                    }">
                    <span class="text-lg font-bold"
                        :class="{
                            'text-red-700 dark:text-red-400': {{ $analysisResults['score'] }} < 4,
                            'text-yellow-700 dark:text-yellow-400': {{ $analysisResults['score'] }} >= 4 && {{ $analysisResults['score'] }} < 7,
                            'text-green-700 dark:text-green-400': {{ $analysisResults['score'] }} >= 7
                        }">
                        {{ $analysisResults['score'] }}/10
                    </span>
                </div>

                <div>
                    <h4 class="font-medium"
                        :class="{
                            'text-red-700 dark:text-red-400': {{ $analysisResults['score'] }} < 4,
                            'text-yellow-700 dark:text-yellow-400': {{ $analysisResults['score'] }} >= 4 && {{ $analysisResults['score'] }} < 7,
                            'text-green-700 dark:text-green-400': {{ $analysisResults['score'] }} >= 7
                        }">
                        @if($analysisResults['score'] < 4)
                            Требуется улучшение
                        @elseif($analysisResults['score'] < 7)
                            Неплохо, но можно лучше
                        @else
                            Отличное описание
                        @endif
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $analysisResults['feedback'] }}</p>
                </div>
            </div>

            <button @click="showDetails = !showDetails" class="text-primary-color hover:text-primary-hover transition-colors">
                <span x-show="!showDetails">Подробнее</span>
                <span x-show="showDetails">Скрыть</span>
            </button>
        </div>

        <!-- Детали анализа (скрыты по умолчанию) -->
        <div x-show="showDetails" x-transition class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
            @if(!empty($analysisResults['strengths']))
                <div class="mb-3">
                    <h5 class="font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Сильные стороны:</h5>
                    <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 ml-2">
                        @foreach($analysisResults['strengths'] as $strength)
                            <li>{{ $strength }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!empty($analysisResults['weaknesses']))
                <div>
                    <h5 class="font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Что можно улучшить:</h5>
                    <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 ml-2">
                        @foreach($analysisResults['weaknesses'] as $weakness)
                            <li>{{ $weakness }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
