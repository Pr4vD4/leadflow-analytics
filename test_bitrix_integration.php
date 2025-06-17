<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\Lead;
use App\Services\Bitrix24Service;

// Загружаем конфигурацию Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Пример тестирования интеграции с Битрикс24
try {
    echo "=== Тест интеграции с Битрикс24 ===\n\n";

    // Найдем первую компанию для теста
    $company = Company::first();

    if (!$company) {
        echo "❌ Не найдена ни одна компания для тестирования\n";
        exit(1);
    }

    echo "✅ Найдена компания: {$company->name} (ID: {$company->id})\n";

    // Проверим настройки интеграции
    $settings = $company->settings ?? [];
    $bitrixEnabled = $settings['integrations']['bitrix24']['enabled'] ?? false;
    $webhookUrl = $settings['integrations']['bitrix24']['webhook_url'] ?? null;

    echo "📊 Статус интеграции Битрикс24:\n";
    echo "   Включено: " . ($bitrixEnabled ? "✅ Да" : "❌ Нет") . "\n";
    echo "   Webhook URL: " . ($webhookUrl ? "✅ {$webhookUrl}" : "❌ Не настроен") . "\n\n";

    if (!$bitrixEnabled || !$webhookUrl) {
        echo "⚠️  Интеграция с Битрикс24 отключена или не настроена.\n";
        echo "   Для тестирования необходимо:\n";
        echo "   1. Зайти в настройки компании\n";
        echo "   2. Включить интеграцию с Битрикс24\n";
        echo "   3. Указать URL вебхука\n\n";
        exit(0);
    }

    // Создаем тестовый лид
    $testLead = new Lead([
        'company_id' => $company->id,
        'source' => 'Тестирование интеграции',
        'name' => 'Тестовый лид',
        'email' => 'test@example.com',
        'phone' => '+7 (999) 123-45-67',
        'message' => 'Это тестовый лид для проверки интеграции с Битрикс24',
        'custom_fields' => [
            'test_field' => 'Тестовое значение'
        ]
    ]);

    echo "🧪 Создан тестовый лид:\n";
    echo "   Имя: {$testLead->name}\n";
    echo "   Email: {$testLead->email}\n";
    echo "   Телефон: {$testLead->phone}\n";
    echo "   Источник: {$testLead->source}\n\n";

    // Тестируем отправку через сервис
    $bitrix24Service = new Bitrix24Service();
    echo "🚀 Отправляем лид в Битрикс24...\n";

    $result = $bitrix24Service->sendLead($testLead);

    if ($result) {
        echo "✅ Лид успешно отправлен в Битрикс24!\n";

        // Проверяем, сохранился ли ID лида в Битрикс24
        $bitrixLeadId = $testLead->custom_fields['bitrix24_lead_id'] ?? null;
        if ($bitrixLeadId) {
            echo "📋 ID лида в Битрикс24: {$bitrixLeadId}\n";
        }
    } else {
        echo "❌ Ошибка при отправке лида в Битрикс24\n";
        echo "   Проверьте логи для получения подробной информации об ошибке\n";
    }

    echo "\n=== Тест завершен ===\n";

} catch (Exception $e) {
    echo "❌ Ошибка при выполнении теста: " . $e->getMessage() . "\n";
    echo "   Трейс: " . $e->getTraceAsString() . "\n";
    exit(1);
}
