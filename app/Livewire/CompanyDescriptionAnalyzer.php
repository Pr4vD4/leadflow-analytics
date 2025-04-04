<?php

namespace App\Livewire;

use App\Services\AI\CompanyDescriptionAnalyzer as AIAnalyzer;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CompanyDescriptionAnalyzer extends Component
{
    /**
     * Текст описания компании для анализа
     */
    public string $description = '';

    /**
     * Минимальная длина текста для анализа
     */
    public int $minLength = 20;

    /**
     * Задержка перед выполнением анализа
     */
    public int $debounceTime = 1000;

    /**
     * Результаты анализа
     */
    public array $analysisResults = [];

    /**
     * Флаг показывающий, что анализ выполняется
     */
    public bool $isAnalyzing = false;

    /**
     * Флаг, указывающий что анализ был выполнен
     */
    public bool $hasAnalyzed = false;

    /**
     * События компонента
     */
    protected $listeners = [
        'companyDescriptionUpdated' => 'setDescription'
    ];

    /**
     * Устанавливает описание для анализа
     */
    public function setDescription(string $text): void
    {
        $this->description = $text;
        $this->analyzeWithDebounce();
    }

    /**
     * Анализирует описание с задержкой
     */
    public function analyzeWithDebounce(): void
    {
        if (strlen($this->description) < $this->minLength) {
            $this->analysisResults = [];
            $this->hasAnalyzed = false;
            $this->isAnalyzing = false;
            return;
        }

        $this->isAnalyzing = true;
        $this->dispatch('debounce', [
            'method' => 'analyze',
            'params' => [],
            'time' => $this->debounceTime
        ]);
    }

    /**
     * Выполняет анализ описания
     */
    public function analyze(): void
    {
        try {
            if (strlen($this->description) < $this->minLength) {
                $this->analysisResults = [];
                $this->hasAnalyzed = false;
                $this->isAnalyzing = false;
                return;
            }

            // Получаем сервис анализатора
            $analyzer = app(AIAnalyzer::class);

            // Выполняем анализ
            $results = $analyzer->analyzeDescription($this->description);

            // Сохраняем результаты
            $this->analysisResults = $results;
            $this->hasAnalyzed = true;

        } catch (\Exception $e) {
            Log::error('Error analyzing company description in Livewire component', [
                'error' => $e->getMessage(),
                'description' => $this->description
            ]);

            $this->analysisResults = [
                'success' => false,
                'score' => null,
                'feedback' => 'Произошла ошибка при анализе описания.'
            ];
        } finally {
            $this->isAnalyzing = false;
        }
    }

    /**
     * Рендеринг компонента
     */
    public function render()
    {
        return view('livewire.company-description-analyzer');
    }
}
