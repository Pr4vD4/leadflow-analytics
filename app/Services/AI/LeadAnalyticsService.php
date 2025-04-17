<?php

namespace App\Services\AI;

use App\Models\Lead;
use App\Models\LeadAnalytics;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LeadAnalyticsService
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
        $this->defaultModel = config('ai.lead_analytics_model', $this->defaultModel);
    }

    /**
     * Генерирует полный аналитический отчет по заявке
     *
     * @param Lead $lead Заявка для анализа
     * @param string|null $model Модель AI для анализа (опционально)
     * @return LeadAnalytics|null Результат анализа или null в случае ошибки
     */
    public function generateAnalytics(Lead $lead, ?string $model = null): ?LeadAnalytics
    {
        // Проверяем доступность Ollama API
        if (!$this->ollamaClient->isAvailable()) {
            Log::warning('Ollama API недоступен при генерации аналитики заявки', [
                'lead_id' => $lead->id
            ]);
            return null;
        }

        // Создаем или получаем запись аналитики
        $analytics = $this->getOrCreateAnalytics($lead);
        $analytics->processing_status = LeadAnalytics::STATUS_PROCESSING;
        $analytics->ai_model_used = $model ?? $this->defaultModel;
        $analytics->save();

        try {
            // Генерируем аналитику
            $prompt = $this->buildAnalyticsPrompt($lead);
            $analysisResult = $this->ollamaClient->generateJson($prompt, $model);

            if (!$analysisResult) {
                $this->markAnalyticsFailed($analytics, 'Не удалось получить результат анализа от AI');
                return null;
            }

            // Обновляем заявку с определенной категорией и кратким содержанием
            $lead->category = $analysisResult['category'] ?? null;
            $lead->summary = $analysisResult['summary'] ?? null;
            $lead->saveQuietly(); // Сохраняем без вызова событий

            // Генерируем ответ
            $responsePrompt = $this->buildResponsePrompt($lead, $analysisResult);
            $generatedResponse = $this->ollamaClient->generateText($responsePrompt, $model);

            // Обновляем запись аналитики
            $analytics->generated_response = $generatedResponse;
            $analytics->analysis_data = $analysisResult;
            $analytics->sentiment = $analysisResult['sentiment'] ?? LeadAnalytics::SENTIMENT_NEUTRAL;
            $analytics->urgency_score = $analysisResult['urgency_score'] ?? null;
            $analytics->complexity_score = $analysisResult['complexity_score'] ?? null;
            $analytics->key_points = $analysisResult['key_points'] ?? null;
            $analytics->processing_status = LeadAnalytics::STATUS_COMPLETED;
            $analytics->save();

            Log::info('Успешно сгенерирована аналитика для заявки', [
                'lead_id' => $lead->id,
                'analytics_id' => $analytics->id
            ]);

            return $analytics;

        } catch (\Exception $e) {
            Log::error('Ошибка при генерации аналитики заявки', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->markAnalyticsFailed($analytics, $e->getMessage());
            return null;
        }
    }

    /**
     * Получает или создает новую запись аналитики для заявки
     *
     * @param Lead $lead
     * @return LeadAnalytics
     */
    protected function getOrCreateAnalytics(Lead $lead): LeadAnalytics
    {
        $analytics = $lead->analytics;

        if (!$analytics) {
            $analytics = new LeadAnalytics();
            $analytics->lead_id = $lead->id;
            $analytics->processing_status = LeadAnalytics::STATUS_PENDING;
            $analytics->save();
        }

        return $analytics;
    }

    /**
     * Отмечает аналитику как неудачно обработанную
     *
     * @param LeadAnalytics $analytics
     * @param string $errorMessage
     * @return void
     */
    protected function markAnalyticsFailed(LeadAnalytics $analytics, string $errorMessage): void
    {
        $analytics->processing_status = LeadAnalytics::STATUS_FAILED;
        $analytics->analysis_data = [
            'error' => $errorMessage,
            'timestamp' => now()->toIso8601String()
        ];
        $analytics->save();

        Log::warning('Анализ заявки отмечен как неудачный', [
            'lead_id' => $analytics->lead_id,
            'analytics_id' => $analytics->id,
            'error' => $errorMessage
        ]);
    }

    /**
     * Строит промпт для анализа заявки
     *
     * @param Lead $lead
     * @return string
     */
    protected function buildAnalyticsPrompt(Lead $lead): string
    {
        // Собираем все доступные данные о заявке
        $name = $lead->name ?? 'Не указано';
        $email = $lead->email ?? 'Не указано';
        $phone = $lead->phone ?? 'Не указано';
        $source = $lead->source ?? 'Не указано';
        $message = $lead->message ?? 'Не указано';

        // Собираем теги
        $tags = $lead->tags->pluck('name')->implode(', ');
        $tagsInfo = !empty($tags) ? "Теги: {$tags}" : "Теги: отсутствуют";

        return <<<PROMPT
Проведи комплексный анализ заявки клиента и подготовь структурированный отчет.

ДАННЫЕ ЗАЯВКИ:
Имя клиента: {$name}
Email: {$email}
Телефон: {$phone}
Источник: {$source}
{$tagsInfo}

Сообщение клиента:
"{$message}"

НЕОБХОДИМО ПРОАНАЛИЗИРОВАТЬ:
1. Определить категорию заявки (например: "Запрос на коммерческое предложение", "Техническая поддержка", "Жалоба", "Партнерство", "Консультация" и т.д.)
2. Создать краткое содержание заявки (1-2 предложения)
3. Тональность сообщения (позитивная, нейтральная, негативная)
4. Срочность запроса (оценка от 1 до 10)
5. Сложность запроса (оценка от 1 до 10)
6. Ключевые моменты запроса (до 3 пунктов)
7. Тип обращения (вопрос, жалоба, благодарность, информационный запрос и т.д.)
8. Потенциальные решения или ответы (что может помочь клиенту)

Верни результат только в формате JSON со следующими полями:
- category: строка, определенная категория заявки
- summary: краткое содержание заявки в одном-двух предложениях (предпочтительно до 10 слов, максимум 20 слов)
- sentiment: строка, одно из значений "positive", "neutral", "negative"
- urgency_score: число от 1 до 10
- complexity_score: число от 1 до 10
- key_points: массив строк (до 3 пунктов)
- request_type: строка, тип обращения
- potential_solutions: массив строк с возможными решениями

Верни только JSON без дополнительного текста.
PROMPT;
    }

    /**
     * Строит промпт для генерации ответа на заявку
     *
     * @param Lead $lead
     * @param array $analysisData
     * @return string
     */
    protected function buildResponsePrompt(Lead $lead, array $analysisData): string
    {
        // Собираем данные о заявке
        $name = $lead->name ?? 'Клиент';
        $message = $lead->message ?? 'Не указано';

        // Получаем тип обращения, тональность и категорию из анализа
        $requestType = $analysisData['request_type'] ?? 'информационный запрос';
        $sentiment = $analysisData['sentiment'] ?? 'neutral';
        $category = $analysisData['category'] ?? 'неопределенная категория';
        $summary = $analysisData['summary'] ?? '';

        // Преобразуем тональность для промпта
        $sentimentText = match($sentiment) {
            'positive' => 'позитивная',
            'negative' => 'негативная',
            default => 'нейтральная'
        };

        return <<<PROMPT
Напиши профессиональный ответ на обращение клиента.

ИНФОРМАЦИЯ О КЛИЕНТЕ:
Имя: {$name}

ОБРАЩЕНИЕ КЛИЕНТА:
"{$message}"

АНАЛИЗ ОБРАЩЕНИЯ:
- Категория: {$category}
- Краткое содержание: {$summary}
- Тип обращения: {$requestType}
- Тональность: {$sentimentText}

ИНСТРУКЦИИ:
1. Напиши вежливый и профессиональный ответ, начинающийся с приветствия.
2. Покажи понимание проблемы/запроса клиента.
3. Предложи конкретное решение или информацию.
4. Заверши ответ предложением дальнейшей помощи и вежливым прощанием.
5. Используй деловой, но дружелюбный тон.
6. Текст должен быть лаконичным (предпочтительно до 150 слов, максимум 200 слов).

ВАЖНО:
- Не придумывай информацию о продуктах или услугах, которых нет в обращении.
- Используй формальный стиль обращения на "Вы".
PROMPT;
    }
}
