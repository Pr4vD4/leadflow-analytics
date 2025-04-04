<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyzeCompanyDescription;
use App\Models\Company;
use App\Services\AI\CompanyDescriptionAnalyzer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompanyAnalysisController extends Controller
{
    /**
     * Анализ описания компании (синхронный запрос)
     *
     * @param Request $request
     * @param CompanyDescriptionAnalyzer $analyzer
     * @return JsonResponse
     */
    public function analyzeDescription(Request $request, CompanyDescriptionAnalyzer $analyzer): JsonResponse
    {
        // Валидируем входные данные
        $validated = $request->validate([
            'description' => 'required|string|min:10|max:2000',
            'company_id' => 'sometimes|exists:companies,id',
        ]);

        // Определяем компанию
        $companyId = $validated['company_id'] ?? null;

        // Если ID компании не передан, используем компанию текущего пользователя
        if (!$companyId && Auth::check()) {
            $companyId = Auth::user()->company_id;
        }

        // Проверяем доступ к компании
        if ($companyId) {
            $company = Company::find($companyId);

            if (!$company || (Auth::check() && Auth::user()->company_id !== $company->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этой компании'
                ], 403);
            }
        }

        try {
            // Выполняем анализ
            $result = $analyzer->analyzeDescription($validated['description']);

            // Если компания существует, запускаем задачу на сохранение результатов
            if ($companyId) {
                // Устанавливаем значение в компании
                $company->description_quality_score = $result['score'];

                // Можно сохранить дополнительные данные в JSON поле, если оно есть
                if (isset($company->ai_analysis)) {
                    $company->ai_analysis = json_encode([
                        'feedback' => $result['feedback'],
                        'strengths' => $result['strengths'],
                        'weaknesses' => $result['weaknesses'],
                        'analyzed_at' => now()->toDateTimeString()
                    ]);
                }

                $company->save();
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error in synchronous company description analysis', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'company_id' => $companyId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при анализе описания компании'
            ], 500);
        }
    }

    /**
     * Постановка задачи на анализ описания компании в очередь (асинхронный запрос)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function queueAnalysis(Request $request): JsonResponse
    {
        // Валидируем входные данные
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id'
        ]);

        $companyId = $validated['company_id'];

        // Проверяем доступ к компании
        $company = Company::find($companyId);
        if (!$company || (Auth::check() && Auth::user()->company_id !== $company->id)) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет доступа к этой компании'
            ], 403);
        }

        // Проверяем наличие описания
        if (empty($company->description)) {
            return response()->json([
                'success' => false,
                'message' => 'Описание компании отсутствует'
            ], 400);
        }

        try {
            // Отправляем задачу в очередь
            AnalyzeCompanyDescription::dispatch($company);

            return response()->json([
                'success' => true,
                'message' => 'Задача на анализ описания компании поставлена в очередь'
            ]);

        } catch (\Exception $e) {
            Log::error('Error queuing company description analysis', [
                'error' => $e->getMessage(),
                'company_id' => $companyId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при постановке задачи в очередь'
            ], 500);
        }
    }

    /**
     * Получение результатов анализа описания компании
     *
     * @param Request $request
     * @param int $companyId
     * @return JsonResponse
     */
    public function getAnalysisResults(Request $request, int $companyId): JsonResponse
    {
        // Проверяем доступ к компании
        $company = Company::find($companyId);
        if (!$company || (Auth::check() && Auth::user()->company_id !== $company->id)) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет доступа к этой компании'
            ], 403);
        }

        // Формируем результат
        $result = [
            'success' => true,
            'score' => $company->description_quality_score ?? null,
        ];

        // Добавляем детали анализа, если они есть
        if (isset($company->ai_analysis)) {
            $analysis = json_decode($company->ai_analysis, true) ?? [];
            $result = array_merge($result, $analysis);
        }

        return response()->json($result);
    }

    /**
     * Полный анализ компании (название + описание)
     *
     * @param Request $request
     * @param CompanyDescriptionAnalyzer $analyzer
     * @return JsonResponse
     */
    public function analyzeCompanyFull(Request $request, CompanyDescriptionAnalyzer $analyzer): JsonResponse
    {
        // Валидируем входные данные
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'description' => 'required|string|min:10|max:2000',
            'model' => 'sometimes|string|max:50', // Опциональная модель AI
        ]);

        Log::info('Запрос на полный анализ компании', [
            'name_length' => strlen($validated['name']),
            'description_length' => strlen($validated['description']),
            'model' => $validated['model'] ?? 'qwen2.5:3b',
            'ip' => $request->ip()
        ]);

        try {
            // Простая проверка на абсурдность перед отправкой на полный анализ
            $absurdKeywords = [
                'космич', 'галакт', 'звезд', 'инопланет', 'вселен', 'марс', 'телепорт', 'путешест', 'времен',
                'единорог', 'дракон', 'магия', 'волшебн', 'эльф', 'гном', 'чудес', 'сказоч', 'нереаль',
                'параллель', 'измерен', 'телепат', 'телекинез', '3000 год', '2999', '2500', 'мульти'
            ];

            $containsAbsurdContent = false;
            foreach ($absurdKeywords as $keyword) {
                if (stripos($validated['name'], $keyword) !== false ||
                    stripos($validated['description'], $keyword) !== false) {
                    $containsAbsurdContent = true;
                    Log::warning('Обнаружен абсурдный/фантастический контент в данных компании', [
                        'keyword' => $keyword,
                        'company_name' => $validated['name']
                    ]);
                    break;
                }
            }

            // Выполняем полный анализ с указанной моделью (если есть)
            $result = $analyzer->analyzeCompanyFull(
                $validated['name'],
                $validated['description'],
                $validated['model'] ?? 'qwen2.5:3b'
            );

            // Дополнительно логируем результат для абсурдных компаний
            if ($containsAbsurdContent) {
                Log::info('Результат анализа потенциально абсурдной компании', [
                    'score' => $result['score'] ?? 'not set',
                    'company_name' => $validated['name'],
                    'is_score_adequate' => ($result['score'] ?? 10) <= 3 ? 'yes' : 'no',
                    'model' => $validated['model'] ?? 'default'
                ]);
            }

            Log::info('Результат анализа компании получен', [
                'success' => isset($result['success']) ? $result['success'] : 'undefined',
                'score' => $result['score'] ?? 'not set',
                'has_strengths' => isset($result['strengths']) && is_array($result['strengths']),
                'has_weaknesses' => isset($result['weaknesses']) && is_array($result['weaknesses']),
                'model' => $validated['model'] ?? 'default'
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error in full company analysis', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'name_length' => strlen($validated['name']),
                'description_length' => strlen($validated['description']),
                'model' => $validated['model'] ?? 'default'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при анализе компании: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получение списка доступных моделей Ollama
     *
     * @param CompanyDescriptionAnalyzer $analyzer
     * @return JsonResponse
     */
    public function getAvailableModels(CompanyDescriptionAnalyzer $analyzer): JsonResponse
    {
        try {
            $models = $analyzer->getAvailableModels();

            if (!$models) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось получить список моделей. Сервис Ollama может быть недоступен.'
                ], 503);
            }

            return response()->json([
                'success' => true,
                'models' => $models
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка моделей', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при получении списка моделей: ' . $e->getMessage()
            ], 500);
        }
    }
}
