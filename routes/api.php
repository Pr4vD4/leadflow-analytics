<?php

use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\API\CompanyAnalysisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Leads API endpoint with API key authentication
Route::middleware('api.key')->group(function () {
    Route::post('/leads', [LeadController::class, 'store']);
});

// AI Analysis API routes
Route::prefix('analysis')->group(function () {
    // Публичный маршрут для анализа описания (без сохранения)
    Route::post('/company-description', [CompanyAnalysisController::class, 'analyzeDescription']);

    // Публичный маршрут для полного анализа компании
    Route::post('/company-full', [CompanyAnalysisController::class, 'analyzeCompanyFull']);

    // Получение списка доступных моделей Ollama
    Route::get('/available-models', [CompanyAnalysisController::class, 'getAvailableModels']);

    // Защищенные маршруты для работы с компаниями
    Route::middleware('auth:sanctum')->group(function () {
        // Постановка задачи анализа в очередь
        Route::post('/company-description/queue', [CompanyAnalysisController::class, 'queueAnalysis']);

        // Получение результатов анализа
        Route::get('/company-description/{companyId}', [CompanyAnalysisController::class, 'getAnalysisResults']);
    });
});
