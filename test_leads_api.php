<?php
/**
 * Скрипт для тестирования API создания заявок в LeadFlow Analytics
 *
 * Этот скрипт отправляет несколько тестовых заявок на эндпоинт /api/leads
 * с различным содержанием и из разных источников.
 */

// Конфигурация
$baseUrl = 'http://localhost:8000'; // Измените на ваш домен
$apiKey = 'demo_api_key_for_testing_purposes_only';

// Примеры заявок с подробным содержанием
$testLeads = [
    // Заявка 1: Запрос на консультацию от клиента с сайта
    [
        'source' => 'website_contact_form',
        'phone' => '+79001234567',
        'email' => 'client@example.com',
        'name' => 'Александр Иванов',
        'message' => 'Добрый день! Наша компания "ТехноСтрой" ищет решение для автоматизации обработки заявок. У нас около 50 заявок в день. Интересует интеграция с нашим сайтом на WordPress и с Bitrix24. Хотелось бы получить демо-доступ и обсудить стоимость внедрения. Можно связаться в рабочее время с 10 до 18 часов.',
        'custom_fields' => [
            'company' => 'ТехноСтрой',
            'position' => 'Технический директор',
            'site' => 'tehnostroy.ru',
            'employees' => '50-100',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'automation_solutions'
        ]
    ],

    // Заявка 2: Жалоба от клиента из Instagram
    [
        'source' => 'instagram_direct',
        'phone' => '+79165554433',
        'name' => 'Елена Петрова',
        'message' => 'Уже второй раз обращаюсь по поводу проблемы с личным кабинетом. После обновления не могу загрузить документы, система выдает ошибку 404. Пыталась через разные браузеры - не помогает. Нужно срочно загрузить договор сегодня до конца дня! Прошу оперативно решить проблему или предложить альтернативный способ отправки документов.',
        'custom_fields' => [
            'client_id' => 'CL-2023-089',
            'priority' => 'high',
            'previous_ticket' => 'T-20230615-42',
            'platform' => 'iOS 16.2',
            'browser' => 'Safari',
            'utm_source' => 'instagram',
            'utm_medium' => 'profile_link'
        ]
    ],

    // Заявка 3: Запрос коммерческого предложения от корпоративного клиента
    [
        'source' => 'landing_page_corporate',
        'email' => 'procurement@bigcorp.ru',
        'name' => 'Сергей Николаев',
        'message' => 'Представляю департамент закупок компании "МегаКорп". Рассматриваем ваше решение для внедрения в нашу систему обработки клиентских обращений. Требуется детальное коммерческое предложение с учетом следующих параметров:\n1. Обработка до 500 заявок в день\n2. Интеграция с SAP и Salesforce\n3. Возможность кастомизации воронки\n4. SLA на техподдержку 24/7\n\nТакже интересует возможность доработки под наши внутренние регламенты безопасности. Бюджет проекта - до 2 млн руб.',
        'custom_fields' => [
            'company' => 'МегаКорп',
            'position' => 'Руководитель отдела закупок',
            'employees' => '1000+',
            'industry' => 'Ритейл',
            'annual_revenue' => '> 1 млрд',
            'timezone' => 'MSK',
            'utm_source' => 'linkedin',
            'utm_medium' => 'sponsored_post',
            'utm_campaign' => 'enterprise_solutions',
            'lead_score' => '95'
        ]
    ],

    // Заявка 4: Техническая поддержка от существующего клиента
    [
        'source' => 'support_portal',
        'phone' => '+79263216549',
        'email' => 'support@client-company.ru',
        'name' => 'Дмитрий Кузнецов',
        'message' => 'При интеграции вашего API с нашей CRM возникла ошибка авторизации. Токен создается успешно, но при отправке запроса получаем ответ 401 Unauthorized. Лог ошибки прикрепляю ниже:\n\n```\nERROR [2023-06-20 15:42:33] Failed to authenticate. Token rejected.\nRequest ID: 8af3bc12-d5e7-4f2a-9876-1234abcd5678\nEndpoint: https://api.leadflow.com/v1/sync\nHeaders: {...}\n```\n\nНаша версия API клиента 2.4.1. Прошу помочь в решении проблемы, так как она блокирует наш релиз на тестовую среду, запланированный на завтра.',
        'custom_fields' => [
            'client_id' => 'CL-2021-156',
            'subscription_plan' => 'Enterprise',
            'technical_contact' => 'tech@client-company.ru',
            'environment' => 'Staging',
            'api_version' => '2.4.1',
            'priority' => 'urgent',
            'support_sla' => '4h',
            'component' => 'API Integration'
        ]
    ],

    // Заявка 5: Потенциальный партнер
    [
        'source' => 'partner_form',
        'phone' => '+79099998877',
        'email' => 'partnerships@digital-agency.ru',
        'name' => 'Анна Соколова',
        'message' => 'Наше агентство "Digital Pro" специализируется на разработке и продвижении сайтов для среднего бизнеса. Ищем надежное решение для наших клиентов в сфере автоматизации обработки заявок.\n\nЗаинтересованы в партнерской программе с возможностью получения комиссионных за привлеченных клиентов. Готовы интегрировать ваше решение в наши проекты (около 15-20 внедрений в год).\n\nПредлагаем также рассмотреть вариант совместных вебинаров и кросс-маркетинговых активностей для наших клиентов из сферы e-commerce и сервисных компаний.',
        'custom_fields' => [
            'company' => 'Digital Pro Agency',
            'position' => 'Директор по развитию',
            'website' => 'digitalpro-agency.ru',
            'employees' => '20-50',
            'clients_count' => '35+',
            'specialization' => 'Web development, Marketing',
            'revenue_share_interest' => 'yes',
            'implementation_capacity' => '15-20 per year',
            'utm_source' => 'partner_conference',
            'utm_medium' => 'business_card'
        ]
    ]
];

/**
 * Отправить заявку на API
 *
 * @param array $leadData Данные заявки
 * @return array Ответ от API
 */
function sendLead($baseUrl, $apiKey, $leadData) {
    $ch = curl_init("$baseUrl/api/leads");

    // Настройка CURL запроса
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($leadData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "X-API-Key: $apiKey"
    ]);

    // Выполнение запроса
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'http_code' => $httpCode,
        'response' => $response ? json_decode($response, true) : null
    ];
}

// Отправка всех тестовых заявок
echo "Начинаем отправку тестовых заявок...\n\n";

foreach ($testLeads as $index => $leadData) {
    $leadNumber = $index + 1;
    echo "Отправка заявки #$leadNumber из источника '{$leadData['source']}'...\n";

    $result = sendLead($baseUrl, $apiKey, $leadData);

    echo "Статус: {$result['http_code']}\n";
    echo "Ответ: " . json_encode($result['response'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
}

echo "Тестирование API завершено!\n";
