<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CompanyDescriptionAnalyzer
{
    protected OllamaClient $ollamaClient;
    protected int $cacheMinutes = 60;

    protected string $defaultModel = 'qwen2.5:3b';

    /**
     * Constructor
     *
     * @param OllamaClient $ollamaClient Клиент для взаимодействия с Ollama API
     */
    public function __construct(OllamaClient $ollamaClient)
    {
        $this->ollamaClient = $ollamaClient;
        $this->defaultModel = config('ai.company_description_model', $this->defaultModel);
    }

    /**
     * Анализ качества описания компании
     *
     * @param string $description Описание компании
     * @param string|null $companyName Название компании (опционально)
     * @param string|null $model Модель AI для анализа (опционально)
     * @return array Результат анализа с оценкой и рекомендациями
     */
    public function analyzeDescription(string $description, ?string $companyName = null, ?string $model = null): array
    {
        // Кэшируем результаты по хэшу описания и названия компании
        $cacheKey = 'company_description_analysis_' . md5($description . ($companyName ?? '') . ($model ?? ''));

        return Cache::remember($cacheKey, $this->cacheMinutes * 60, function () use ($description, $companyName, $model) {
            $prompt = $this->buildAnalysisPrompt($description, $companyName);

            try {
                $result = $this->ollamaClient->generateJson($prompt, $model);

                if (!$result) {
                    Log::warning('Failed to analyze company description, using fallback data', [
                        'description_length' => strlen($description),
                        'company_name' => $companyName,
                        'model' => $model ?? 'default'
                    ]);
                    return $this->getFallbackAnalysisData($description, $companyName);
                }

                return $this->normalizeAnalysisResult($result);
            } catch (\Exception $e) {
                Log::error('Error analyzing company description', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'model' => $model ?? 'default'
                ]);
                return $this->getFallbackAnalysisData($description, $companyName);
            }
        });
    }

    /**
     * Полный анализ компании, включая название и описание
     *
     * @param string $companyName Название компании
     * @param string $description Описание компании
     * @param string|null $model Модель AI для анализа (опционально)
     * @return array Результат анализа с оценкой и рекомендациями
     */
    public function analyzeCompanyFull(string $companyName, string $description, ?string $model = null): array
    {
        // Проверяем доступность Ollama API
        if (!$this->ollamaClient->isAvailable()) {
            Log::warning('Ollama API недоступен, используем локальный анализ', [
                'company_name' => $companyName,
                'description_length' => strlen($description)
            ]);
            return $this->getFallbackAnalysisData($description, $companyName);
        }

        // Расширенный промпт для полного анализа
        $prompt = $this->buildFullAnalysisPrompt($companyName, $description);

        // Кэшируем результаты
        $cacheKey = 'company_full_analysis_' . md5($companyName . '_' . $description . '_' . ($model ?? ''));

        // Проверка наличия данных в кэше
        if (Cache::has($cacheKey)) {
            Log::info('Использованы кэшированные данные для анализа компании', [
                'cache_key' => $cacheKey,
                'company_name_length' => strlen($companyName),
                'model' => $model ?? 'default'
            ]);
        }

        return Cache::remember($cacheKey, $this->cacheMinutes * 60, function () use ($prompt, $companyName, $description, $model) {
            try {
                Log::info('Отправка запроса на Ollama API', [
                    'prompt_length' => strlen($prompt),
                    'company_name_length' => strlen($companyName),
                    'description_length' => strlen($description),
                    'model' => $model ?? 'default'
                ]);

                $result = $this->ollamaClient->generateJson($prompt, $model);

                if (!$result) {
                    Log::warning('Не удалось выполнить полный анализ компании через Ollama API', [
                        'company_name' => $companyName,
                        'description_length' => strlen($description),
                        'model' => $model ?? 'default'
                    ]);
                    // Используем тот же метод для заглушки
                    return $this->getFallbackAnalysisData($description, $companyName);
                }

                Log::info('Успешно получены данные анализа от Ollama API', [
                    'result_keys' => is_array($result) ? array_keys($result) : 'not_array',
                    'score' => $result['score'] ?? 'not_set',
                    'model' => $model ?? 'default'
                ]);

                return $this->normalizeAnalysisResult($result);
            } catch (\Exception $e) {
                Log::error('Ошибка в полном анализе компании', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'class' => get_class($e),
                    'model' => $model ?? 'default'
                ]);
                return $this->getFallbackAnalysisData($description, $companyName);
            }
        });
    }

    /**
     * Возвращает список доступных моделей Ollama
     *
     * @return array|null Список доступных моделей или null в случае ошибки
     */
    public function getAvailableModels(): ?array
    {
        try {
            return $this->ollamaClient->listRunningModels();
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка доступных моделей', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Построение промпта для анализа описания
     *
     * @param string $description Описание компании
     * @param string|null $companyName Название компании (опционально)
     * @return string Промпт для модели
     */
    protected function buildAnalysisPrompt(string $description, ?string $companyName = null): string
    {
        $companyNameSection = $companyName ? "Название компании: \"{$companyName}\"" : "";

        return <<<PROMPT
Проанализируй следующее описание компании с ПРИОРИТЕТНЫМ вниманием к РЕАЛИСТИЧНОСТИ.

{$companyNameSection}
Описание компании:
"{$description}"

КРИТЕРИИ ОЦЕНКИ (по приоритету):
1. РЕАЛИСТИЧНОСТЬ (критично важный параметр):
   - Данные ДОЛЖНЫ описывать ТОЛЬКО реально возможный бизнес
   - Полное отсутствие фантастических элементов (космические технологии, магия, параллельные вселенные)
   - Отсутствие абсурдных утверждений (невозможные функции продуктов, нереальные даты)

2. Только если описание реалистично, оцени:
   - Информативность - насколько полно описаны услуги/продукты
   - Профессиональность - деловой стиль, отсутствие разговорных выражений
   - Конкретика - наличие измеримых преимуществ, а не общих фраз

ПРАВИЛА ОЦЕНКИ:
- Оценка 1-2: ОБЯЗАТЕЛЬНО для откровенно абсурдных, фантастических, нереальных описаний
- Оценка 3-4: для описаний с элементами фантастики или серьезными логическими противоречиями
- Оценка 5-6: для реалистичных, но посредственных описаний с недостатком конкретики
- Оценка 7: для полностью реалистичных описаний с базовой конкретикой
- Оценка 8-9: для реалистичных, хорошо структурированных описаний с конкретными данными (цифры, даты, факты)
- Оценка 10: для безупречных, профессиональных описаний с отличной структурой, измеримыми показателями и четким УТП

ВАЖНО: если в названии или описании есть хоть один нереалистичный/фантастический элемент, оценка НЕ МОЖЕТ быть выше 4!

Верни ответ только в формате JSON со следующими полями:
- score: числовая оценка от 1 до 10 (с приоритетом реалистичности)
- feedback: краткая рекомендация по улучшению (1-2 предложения)
- strengths: массив сильных сторон (не более 2 пунктов, только для реалистичных описаний)
- weaknesses: массив слабых сторон (не более 3 пунктов)

Верни только JSON без дополнительного текста.
PROMPT;
    }

    /**
     * Построение промпта для полного анализа компании
     *
     * @param string $companyName Название компании
     * @param string $description Описание компании
     * @return string Промпт для модели
     */
    protected function buildFullAnalysisPrompt(string $companyName, string $description): string
    {
        return <<<PROMPT
Выполни анализ компании, уделяя ОСОБОЕ внимание РЕАЛИСТИЧНОСТИ данных:

Название компании: "{$companyName}"
Описание компании: "{$description}"

КРИТЕРИИ ОЦЕНКИ (по приоритету):
1. РЕАЛИСТИЧНОСТЬ (критично важный параметр):
   - Данные ДОЛЖНЫ описывать ТОЛЬКО реально возможный бизнес
   - Полное отсутствие фантастических элементов (космические технологии, магия, параллельные вселенные)
   - Отсутствие абсурдных утверждений (невозможные функции продуктов, нереальные даты)

2. Только если описание реалистично, оцени:
   - Информативность - насколько полно описаны услуги/продукты
   - Профессиональность - деловой стиль, отсутствие разговорных выражений
   - Конкретика - наличие измеримых преимуществ, а не общих фраз

ПРАВИЛА ОЦЕНКИ:
- Оценка 1-2: ОБЯЗАТЕЛЬНО для откровенно абсурдных, фантастических, нереальных описаний
- Оценка 3-4: для описаний с элементами фантастики или серьезными логическими противоречиями
- Оценка 5-6: для реалистичных, но посредственных описаний с недостатком конкретики
- Оценка 7: для полностью реалистичных описаний с базовой конкретикой
- Оценка 8-9: для реалистичных, хорошо структурированных описаний с конкретными данными (цифры, даты, факты)
- Оценка 10: для безупречных, профессиональных описаний с отличной структурой, измеримыми показателями и четким УТП

ВАЖНО: если в названии или описании есть хоть один нереалистичный/фантастический элемент, оценка НЕ МОЖЕТ быть выше 4!

Верни ответ только в формате JSON со следующими полями:
- score: числовая оценка от 1 до 10 (с приоритетом реалистичности)
- feedback: краткая рекомендация по улучшению (1-2 предложения)
- strengths: массив сильных сторон (не более 2 пунктов, только для реалистичных описаний)
- weaknesses: массив слабых сторон (не более 3 пунктов)

Верни только JSON без дополнительного текста.
PROMPT;
    }

    /**
     * Получение результата анализа по умолчанию (при ошибке)
     *
     * @return array Результат анализа по умолчанию
     */
    protected function getDefaultAnalysisResult(): array
    {
        return [
            'success' => false,
            'score' => 5,
            'feedback' => 'Не удалось выполнить анализ описания. Пожалуйста, попробуйте позже.',
            'strengths' => [],
            'weaknesses' => []
        ];
    }

    /**
     * Нормализация результата анализа
     *
     * @param array|null $result Результат от API
     * @return array Нормализованный результат
     */
    protected function normalizeAnalysisResult(?array $result): array
    {
        if (!$result) {
            return $this->getDefaultAnalysisResult();
        }

        // Проверяем наличие всех необходимых полей
        $score = $result['score'] ?? 5;
        $feedback = $result['feedback'] ?? 'Нет рекомендаций.';
        $strengths = $result['strengths'] ?? [];
        $weaknesses = $result['weaknesses'] ?? [];

        // Убеждаемся, что оценка в диапазоне от 1 до 10
        $score = max(1, min(10, (int)$score));

        return [
            'success' => true,
            'score' => $score,
            'feedback' => $feedback,
            'strengths' => $strengths,
            'weaknesses' => $weaknesses
        ];
    }

    /**
     * Получение тестовых данных анализа для режима разработки или при недоступности сервиса ИИ
     *
     * @param string $description Описание компании
     * @param string|null $companyName Название компании
     * @return array Результат анализа
     */
    protected function getFallbackAnalysisData(string $description, ?string $companyName = null): array
    {
        // Базовая оценка начинается с 5 (среднее значение)
        $score = 5;

        // Проверка на абсурдность/фантастичность в названии и описании
        // Расширенный список ключевых слов для выявления нереалистичных описаний
        $absurdKeywords = [
            // Космос и инопланетное
            'космич', 'галакт', 'звезд', 'инопланет', 'вселен', 'марс', 'телепорт', 'черн[а-я]+ дыр',
            'межзвезд', 'звездолет', 'нло', 'пришелец', 'внеземн', 'планет[а-я]+ существ',
            // Магия и фэнтези
            'единорог', 'дракон', 'магия', 'волшебн', 'эльф', 'гном', 'чудес', 'сказоч', 'нереаль',
            // Путешествия во времени и параллельные миры
            'параллель', 'измерен', 'путешеств[а-я]+ во времен', 'машин[а-я]+ времен', 'будущ[а-я]+ век',
            // Сверхспособности
            'телепат', 'телекинез', 'невидим', 'суперсил', 'бессмерт', 'всемогущ',
            // Нереальные даты и числа
            '3000 год', '2500', '2999', 'миллиард лет', 'вечн[а-я]+ жизн', 'мульти',
            // Абсурдные технологии
            'квантов[а-я]+ пылесос', 'энерги[а-я]+ улыбок', 'несуществующ[а-я]+ частиц'
        ];

        // Строгая проверка на абсурдность
        $absurdMatch = false;
        $absurdKeywordFound = null;

        // Проверяем и название, и описание на абсурдные ключевые слова
        $textToCheck = strtolower($description . ' ' . ($companyName ?? ''));

        foreach ($absurdKeywords as $keyword) {
            if (preg_match('/'. $keyword .'/ui', $textToCheck)) {
                $absurdMatch = true;
                $absurdKeywordFound = $keyword;
                // Резко снижаем оценку до 1-2 при обнаружении явно абсурдных элементов
                $score = 2;
                break;
            }
        }

        // Если нет явно абсурдных элементов, продолжаем оценку
        if (!$absurdMatch) {
            // Проверка наличия бизнес-терминов
            $businessKeywords = [
                'услуг', 'клиент', 'опыт', 'специалист', 'качеств', 'цен', 'работ', 'профессион',
                'рынок', 'бизнес', 'компетен', 'решен', 'сервис', 'партнер', 'технолог',
                'разработ', 'продукт', 'поддерж', 'консульт', 'проект', 'с 20[0-2][0-9]'
            ];

            // Повышаем оценку при обнаружении бизнес-терминов
            $businessTermsCount = 0;
            foreach ($businessKeywords as $keyword) {
                if (preg_match('/'. $keyword .'/ui', $textToCheck)) {
                    $businessTermsCount++;
                }
            }

            // Учитываем количество бизнес-терминов и длину описания
            $length = strlen($description);
            $wordCount = str_word_count(preg_replace('/[^a-zA-Zа-яА-ЯёЁ\s]/u', '', $description));

            // Модифицируем оценку на основе деловых терминов (но не повышаем выше 7 без участия AI)
            if ($businessTermsCount >= 3) $score += 1;
            if ($businessTermsCount >= 6) $score += 1;

            // Длина описания влияет на оценку, но не более +1
            if ($length > 200 && $wordCount > 40) $score += 1;

            // Ограничиваем максимальный балл для локального анализа
            $score = min(7, $score);
        }

        // Формируем результат
        $result = [
            'success' => true,
            'score' => $score,
            'feedback' => 'Автоматический анализ описания. Для более детального анализа необходимо подключение к серверу ИИ.',
            'strengths' => [],
            'weaknesses' => []
        ];

        // Формируем сильные и слабые стороны в зависимости от оценки
        if ($score <= 2) {
            // Для абсурдных описаний
            $result['feedback'] = 'Описание содержит нереалистичные или фантастические элементы. Необходимо полностью переработать контент.';
            $result['weaknesses'][] = 'Описание содержит нереалистичные или фантастические элементы';
            $result['weaknesses'][] = 'Описание не соответствует формату делового представления компании';
            if ($absurdKeywordFound) {
                $result['weaknesses'][] = "Обнаружены ключевые слова, указывающие на нереалистичность: '{$absurdKeywordFound}'";
            }
        } elseif ($score <= 4) {
            // Для описаний с элементами фантастики
            $result['feedback'] = 'Описание содержит элементы, которые снижают его деловую ценность. Рекомендуется сделать его более реалистичным.';
            $result['weaknesses'][] = 'Описание содержит сомнительные или нереалистичные утверждения';
            $result['weaknesses'][] = 'Необходимо сосредоточиться на фактических преимуществах компании';
        } elseif ($score <= 6) {
            // Для реалистичных, но недостаточно информативных описаний
            $result['feedback'] = 'Описание реалистично, но недостаточно информативно. Рекомендуется добавить конкретные детали.';
            $result['strengths'][] = 'Реалистичное описание компании';
            if ($businessTermsCount >= 3) {
                $result['strengths'][] = 'Использование профессиональной терминологии';
            }
            $result['weaknesses'][] = 'Недостаточно информации о конкретных преимуществах компании';
        } else {
            // Для хороших реалистичных описаний
            $result['feedback'] = 'Хорошее реалистичное описание. Можно усилить конкретными цифрами и фактами.';
            $result['strengths'][] = 'Реалистичное и профессиональное описание';
            $result['strengths'][] = 'Хорошо структурированная информация';
            $result['weaknesses'][] = 'Можно добавить больше измеримых результатов и конкретных примеров';
        }

        return $result;
    }
}
