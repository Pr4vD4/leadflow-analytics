<?php

namespace App\Jobs;

use App\Models\Company;
use App\Services\AI\CompanyDescriptionAnalyzer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeCompanyDescription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Количество попыток выполнения задачи
     */
    public $tries = 2;

    /**
     * Приоритет задачи
     */
    public $priority;

    /**
     * ID компании
     */
    protected $companyId;

    /**
     * Текст описания для анализа
     */
    protected $description;

    /**
     * Создает новый экземпляр задачи
     *
     * @param Company $company Компания для анализа
     * @return void
     */
    public function __construct(Company $company)
    {
        $this->companyId = $company->id;
        $this->description = $company->description;
        $this->queue = config('ai.queue_name', 'ai-processing');
        $this->priority = config('ai.queue_priority', 10);
    }

    /**
     * Выполнение задачи
     *
     * @param CompanyDescriptionAnalyzer $analyzer
     * @return void
     */
    public function handle(CompanyDescriptionAnalyzer $analyzer): void
    {
        try {
            // Проверяем минимальную длину текста
            $minLength = config('ai.min_text_length', 10);
            if (strlen($this->description) < $minLength) {
                Log::info('Company description too short for analysis', [
                    'company_id' => $this->companyId,
                    'length' => strlen($this->description)
                ]);
                return;
            }

            // Анализируем описание
            $result = $analyzer->analyzeDescription($this->description);

            // Если анализ выполнен успешно, сохраняем результаты
            if ($result['success']) {
                $company = Company::find($this->companyId);

                if ($company) {
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

                    Log::info('Successfully analyzed company description', [
                        'company_id' => $this->companyId,
                        'score' => $result['score']
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error analyzing company description', [
                'company_id' => $this->companyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // При необходимости можно вызвать исключение для повторного запуска задачи
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }
}
