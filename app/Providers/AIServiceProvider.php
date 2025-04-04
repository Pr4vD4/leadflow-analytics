<?php

namespace App\Providers;

use App\Services\AI\OllamaClient;
use App\Services\AI\CompanyDescriptionAnalyzer;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Регистрация OllamaClient
        $this->app->singleton(OllamaClient::class, function ($app) {
            return new OllamaClient(
                config('ai.ollama_url', 'http://localhost:11434'),
                config('ai.request_timeout', 30),
                config('ai.default_model', 'qwen2.5:1.5b')
            );
        });

        // Регистрация CompanyDescriptionAnalyzer
        $this->app->singleton(CompanyDescriptionAnalyzer::class, function ($app) {
            return new CompanyDescriptionAnalyzer(
                $app->make(OllamaClient::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
