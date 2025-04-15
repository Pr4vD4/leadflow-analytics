<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\LeadMetric;
use Illuminate\Console\Command;

class UpdateLeadMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:update-metrics
                            {--company= : ID компании (не указывайте для обновления всех компаний)}
                            {--force : Принудительное обновление всех метрик}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет метрики конверсии и релевантности для всех заявок';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->option('company');
        $force = $this->option('force');

        if ($companyId) {
            // Обновляем метрики для одной компании
            $this->updateMetricsForCompany((int) $companyId, $force);
        } else {
            // Обновляем метрики для всех компаний
            $companies = Company::all();
            $count = $companies->count();

            $this->info("Обновление метрик для {$count} компаний...");
            $progressBar = $this->output->createProgressBar($count);
            $progressBar->start();

            foreach ($companies as $company) {
                $this->updateMetricsForCompany($company->id, $force);
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine();
            $this->info('Метрики успешно обновлены для всех компаний.');
        }

        return Command::SUCCESS;
    }

    /**
     * Обновляет метрики для указанной компании.
     *
     * @param int $companyId
     * @param bool $force
     * @return void
     */
    private function updateMetricsForCompany(int $companyId, bool $force): void
    {
        try {
            $updatedMetrics = LeadMetric::updateCompanyMetrics($companyId, $force);
            $count = count($updatedMetrics);

            if ($this->option('company')) {
                $this->info("Успешно обновлено {$count} метрик для компании #{$companyId}.");
            }
        } catch (\Exception $e) {
            if ($this->option('company')) {
                $this->error("Ошибка при обновлении метрик для компании #{$companyId}: {$e->getMessage()}");
            }
        }
    }
}
