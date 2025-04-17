<?php

namespace App\Providers;

use App\Models\Lead;
use App\Models\LeadComment;
use App\Observers\LeadObserver;
use App\Observers\LeadCommentObserver;
use App\Services\AI\LeadAnalyticsService;
use App\Services\AI\LeadRelevanceAnalyzer;
use App\Services\AI\OllamaClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрируем OllamaClient как синглтон
        $this->app->singleton(OllamaClient::class, function ($app) {
            $baseUrl = config('ai.ollama_api_url', 'http://localhost:11434');
            $timeout = config('ai.ollama_timeout', 30);
            $defaultModel = config('ai.default_model', 'qwen2.5:1.5b');

            return new OllamaClient($baseUrl, $timeout, $defaultModel);
        });

        // Регистрируем LeadRelevanceAnalyzer
        $this->app->singleton(LeadRelevanceAnalyzer::class, function ($app) {
            return new LeadRelevanceAnalyzer($app->make(OllamaClient::class));
        });

        // Регистрируем LeadAnalyticsService
        $this->app->singleton(LeadAnalyticsService::class, function ($app) {
            return new LeadAnalyticsService($app->make(OllamaClient::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Регистрируем наблюдатель для модели Lead
        Lead::observe(LeadObserver::class);

        // Регистрируем наблюдатель для модели LeadComment
        LeadComment::observe(LeadCommentObserver::class);
    }
}
