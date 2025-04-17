<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaClient
{
    protected string $baseUrl;
    protected int $timeout;
    protected string $defaultModel;

    /**
     * OllamaClient constructor
     *
     * @param string $baseUrl URL Ollama API (по умолчанию: http://localhost:11434)
     * @param int $timeout Таймаут для запросов в секундах
     * @param string $defaultModel Модель по умолчанию
     */
    public function __construct(
        string $baseUrl = 'http://localhost:11434',
        int $timeout = 30,
        string $defaultModel = 'qwen2.5:1.5b'
    ) {
        $this->baseUrl = $baseUrl;
        $this->timeout = $timeout;
        $this->defaultModel = $defaultModel;
    }

    /**
     * Генерация текста с использованием заданной модели
     *
     * @param string $prompt Текстовый запрос
     * @param string|null $model Название модели (если null, используется модель по умолчанию)
     * @param array $options Дополнительные параметры модели (temperature, etc)
     * @return string|null Результат генерации или null в случае ошибки
     */
    public function generateText(string $prompt, ?string $model = null, array $options = []): ?string
    {
        $modelName = $model ?? $this->defaultModel;

        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/generate", [
                    'model' => $modelName,
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => (object) $options
                ]);

            if ($response->failed()) {
                Log::error('Ollama API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            return $data['response'] ?? null;

        } catch (\Exception $e) {
            Log::error('Ollama API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Генерация JSON с использованием заданной модели
     *
     * @param string $prompt Текстовый запрос
     * @param string|null $model Название модели (если null, используется модель по умолчанию)
     * @param array $options Дополнительные параметры модели
     * @return array|null JSON результат или null в случае ошибки
     */
    public function generateJson(string $prompt, ?string $model = null, array $options = []): ?array
    {
        try {
            Log::debug('Запрос к Ollama API для генерации JSON', [
                'base_url' => $this->baseUrl,
                'model' => $model ?? $this->defaultModel,
                'prompt_length' => strlen($prompt),
                'timeout' => $this->timeout
            ]);

            $modelName = $model ?? $this->defaultModel;
            $requestStart = microtime(true);

            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/generate", [
                    'model' => $modelName,
                    'prompt' => $prompt,
                    'format' => 'json',
                    'stream' => false,
                    'options' => (object) $options
                ]);

            $requestTime = microtime(true) - $requestStart;
            Log::debug('Ответ от Ollama API получен', [
                'status' => $response->status(),
                'time' => round($requestTime, 2) . 's',
                'headers' => $response->headers(),
                'model' => $modelName
            ]);

            if ($response->failed()) {
                Log::error('Ollama API error (JSON generation)', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => "{$this->baseUrl}/api/generate",
                    'reason' => $response->reason(),
                    'model' => $modelName
                ]);
                return null;
            }

            $data = $response->json();
            $jsonResponse = $data['response'] ?? null;

            if (!$jsonResponse) {
                Log::warning('Пустой ответ от Ollama API', [
                    'data_keys' => array_keys($data),
                    'model' => $modelName
                ]);
                return null;
            }

            // Преобразуем строку JSON в массив
            $decodedJson = json_decode($jsonResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Ошибка декодирования JSON от Ollama API', [
                    'error' => json_last_error_msg(),
                    'raw_response' => substr($jsonResponse, 0, 100) . '...',
                    'model' => $modelName
                ]);
                return null;
            }

            return $decodedJson;

        } catch (\Exception $e) {
            Log::error('Ollama API exception (JSON generation)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'class' => get_class($e),
                'url' => $this->baseUrl ?? 'undefined'
            ]);
            return null;
        }
    }

    /**
     * Создание эмбеддингов для текста
     *
     * @param string|array $input Текст или массив текстов для создания эмбеддингов
     * @param string|null $model Название модели для эмбеддингов
     * @return array|null Массив эмбеддингов или null в случае ошибки
     */
    public function createEmbeddings($input, ?string $model = null): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/embed", [
                    'model' => $model ?? 'bge-m3',
                    'input' => $input
                ]);

            if ($response->failed()) {
                Log::error('Ollama API error (embeddings)', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            return $data['embeddings'] ?? null;

        } catch (\Exception $e) {
            Log::error('Ollama API exception (embeddings)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Получение списка запущенных моделей
     *
     * @return array|null Список моделей или null в случае ошибки
     */
    public function listRunningModels(): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/ps");

            if ($response->failed()) {
                Log::error('Ollama API error (list models)', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();
            return $data['models'] ?? [];

        } catch (\Exception $e) {
            Log::error('Ollama API exception (list models)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Проверяет доступность Ollama API
     *
     * @return bool Возвращает true, если API доступен
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5) // короткий таймаут для быстрой проверки
                ->get("{$this->baseUrl}/api/ps");

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Ollama API недоступен', [
                'message' => $e->getMessage(),
                'url' => $this->baseUrl
            ]);
            return false;
        }
    }
}
