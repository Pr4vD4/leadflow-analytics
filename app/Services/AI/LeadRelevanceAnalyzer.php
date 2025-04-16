<?php

namespace App\Services\AI;

use App\Models\Lead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LeadRelevanceAnalyzer
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
        $this->defaultModel = config('ai.lead_relevance_model', $this->defaultModel);
    }

    /**
     * Анализирует заявку и определяет ее релевантность от 1 до 10
     *
     * @param Lead $lead Заявка для анализа
     * @param string|null $model Модель AI для анализа (опционально)
     * @return int|null Оценка релевантности от 1 до 10 или null в случае ошибки
     */
    public function analyzeLead(Lead $lead, ?string $model = null): ?int
    {
        // Кэшируем результаты по ID заявки
        $cacheKey = 'lead_relevance_' . $lead->id;

        return Cache::remember($cacheKey, $this->cacheMinutes * 60, function () use ($lead, $model) {
            // Проверяем доступность Ollama API
            if (!$this->ollamaClient->isAvailable()) {
                Log::warning('Ollama API недоступен при анализе релевантности заявки', [
                    'lead_id' => $lead->id
                ]);
                return null;
            }

            $prompt = $this->buildPrompt($lead);

            try {
                Log::info('Отправка запроса на Ollama API для анализа релевантности заявки', [
                    'lead_id' => $lead->id,
                    'prompt_length' => strlen($prompt),
                    'model' => $model ?? $this->defaultModel
                ]);

                $result = $this->ollamaClient->generateJson($prompt, $model);

                if (!$result || !isset($result['score'])) {
                    Log::warning('Не удалось получить оценку релевантности заявки', [
                        'lead_id' => $lead->id,
                        'result' => $result,
                        'model' => $model ?? $this->defaultModel
                    ]);
                    return null;
                }

                $score = (int)$result['score'];

                // Проверяем, что оценка находится в диапазоне от 1 до 10
                if ($score < 1 || $score > 10) {
                    Log::warning('Полученная оценка релевантности вне допустимого диапазона', [
                        'lead_id' => $lead->id,
                        'score' => $score,
                        'model' => $model ?? $this->defaultModel
                    ]);
                    $score = max(1, min(10, $score)); // Ограничиваем значение диапазоном 1-10
                }

                Log::info('Успешно получена оценка релевантности заявки', [
                    'lead_id' => $lead->id,
                    'score' => $score,
                    'model' => $model ?? $this->defaultModel
                ]);

                return $score;
            } catch (\Exception $e) {
                Log::error('Ошибка при анализе релевантности заявки', [
                    'lead_id' => $lead->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'model' => $model ?? $this->defaultModel
                ]);
                return null;
            }
        });
    }

    /**
     * Обновляет оценку релевантности для заявки и сохраняет её в базе данных
     *
     * @param Lead $lead Заявка для обновления
     * @param string|null $model Модель AI для анализа (опционально)
     * @return bool Успешно ли обновлена оценка
     */
    public function updateLeadRelevance(Lead $lead, ?string $model = null): bool
    {
        $relevanceScore = $this->analyzeLead($lead, $model);

        if ($relevanceScore === null) {
            return false;
        }

        try {
            $lead->relevance_score = $relevanceScore;
            $lead->save();

            Log::info('Обновлена оценка релевантности заявки', [
                'lead_id' => $lead->id,
                'score' => $relevanceScore
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Ошибка при сохранении оценки релевантности', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Построение промпта для анализа релевантности заявки
     *
     * @param Lead $lead Заявка для анализа
     * @return string Промпт для модели
     */
    protected function buildPrompt(Lead $lead): string
    {
        // Собираем все доступные данные о заявке
        $name = $lead->name ?? 'Не указано';
        $email = $lead->email ?? 'Не указано';
        $phone = $lead->phone ?? 'Не указано';
        $source = $lead->source ?? 'Не указано';
        $message = $lead->message ?? 'Не указано';
        $category = $lead->category ?? 'Не определена';

        // Собираем теги, если они есть
        $tags = $lead->tags->pluck('name')->implode(', ');
        $tagsInfo = !empty($tags) ? "Теги: {$tags}" : "Теги: отсутствуют";

        return <<<PROMPT
Оцени релевантность заявки от клиента по шкале от 1 до 10, где:
1-3: низкая релевантность (спам, нерелевантные запросы)
4-6: средняя релевантность (общие вопросы, требующие уточнения)
7-10: высокая релевантность (конкретные запросы, готовность к сотрудничеству)

ДАННЫЕ ЗАЯВКИ:
Имя клиента: {$name}
Email: {$email}
Телефон: {$phone}
Источник: {$source}
{$tagsInfo}
Категория: {$category}

Сообщение клиента:
"{$message}"

КРИТЕРИИ ОЦЕНКИ РЕЛЕВАНТНОСТИ:
1. Конкретность запроса (общие фразы = ниже, конкретные детали = выше)
2. Полнота контактных данных (больше контактов = выше оценка)
3. Соответствие тематике бизнеса (насколько запрос соответствует услугам компании)
4. Наличие признаков реального интереса (а не спама или холодного обращения)
5. Потенциал для конверсии в продажу или длительное сотрудничество

Верни результат только в формате JSON с полями:
- score: числовая оценка релевантности от 1 до 10 (целое число)
- explanation: краткое объяснение оценки (1-2 предложения)

Верни только JSON без дополнительного текста.
PROMPT;
    }
}
