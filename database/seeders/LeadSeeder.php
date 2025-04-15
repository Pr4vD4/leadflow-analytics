<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Lead;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::first();

        if (!$company) {
            $this->command->error('Необходимо сначала создать компанию! Запустите CompanySeeder.');
            return;
        }

        $sources = ['website', 'facebook', 'instagram', 'direct', 'google', 'referral'];
        $statuses = ['new', 'in_progress', 'completed', 'archived'];
        $categories = ['запрос', 'жалоба', 'предложение'];

        // Создаем 50 тестовых лидов
        for ($i = 1; $i <= 50; $i++) {
            $source = $sources[array_rand($sources)];
            $status = $statuses[array_rand($statuses)];
            $category = $categories[array_rand($categories)];
            $hasEmail = rand(0, 10) > 3; // 70% лидов имеют email
            $hasPhone = rand(0, 10) > 2; // 80% лидов имеют телефон

            $customFields = [
                'utm_source' => ['google', 'direct', 'social'][array_rand(['google', 'direct', 'social'])],
                'utm_medium' => ['cpc', 'organic', 'referral'][array_rand(['cpc', 'organic', 'referral'])],
                'utm_campaign' => ['summer_sale', 'new_products', 'promo'][array_rand(['summer_sale', 'new_products', 'promo'])],
                'device' => ['mobile', 'desktop', 'tablet'][array_rand(['mobile', 'desktop', 'tablet'])],
            ];

            // Выбираем случайное число от 1 до 10 для relevance_score
            $relevanceScore = rand(1, 10);

            // Генерируем различные сообщения в зависимости от категории
            $message = '';
            switch ($category) {
                case 'запрос':
                    $message = [
                        'Интересует подробная информация о вашем продукте. Можете рассказать о ценах и условиях доставки?',
                        'Хочу узнать о возможности сотрудничества с вашей компанией. Какие условия вы предлагаете?',
                        'Нужна консультация по выбору оптимального тарифа для моего бизнеса.',
                        'Планирую крупный заказ, нужна информация о скидках для оптовых клиентов.'
                    ][array_rand([0, 1, 2, 3])];
                    break;
                case 'жалоба':
                    $message = [
                        'Заказ №' . rand(1000, 9999) . ' до сих пор не доставлен. Прошло уже 5 дней! Требую разъяснений.',
                        'Качество обслуживания оставляет желать лучшего. Менеджер был неприветлив и не смог ответить на мои вопросы.',
                        'Товар пришел с дефектом. Хочу вернуть деньги или заменить на качественный продукт.',
                        'Второй раз сталкиваюсь с проблемой в вашем приложении. Постоянные ошибки и вылеты!'
                    ][array_rand([0, 1, 2, 3])];
                    break;
                case 'предложение':
                    $message = [
                        'У меня есть идея по улучшению вашего сервиса. Могу я обсудить её с вашими специалистами?',
                        'Предлагаю партнерство по размещению вашей рекламы на моем сайте. Трафик более 10000 посетителей в день.',
                        'Хочу предложить поставку наших товаров для вашего магазина по оптовым ценам.',
                        'Есть предложение по оптимизации вашего бизнес-процесса, которое может сэкономить до 30% затрат.'
                    ][array_rand([0, 1, 2, 3])];
                    break;
            }

            // Генерируем краткое описание (summary)
            $summary = match($category) {
                'запрос' => 'Запрос информации о ' . ['продукте', 'услуге', 'сотрудничестве', 'ценах'][array_rand([0, 1, 2, 3])],
                'жалоба' => 'Жалоба на ' . ['доставку', 'обслуживание', 'качество', 'работу приложения'][array_rand([0, 1, 2, 3])],
                'предложение' => 'Предложение о ' . ['партнерстве', 'сотрудничестве', 'размещении рекламы', 'оптимизации'][array_rand([0, 1, 2, 3])],
                default => 'Краткое описание обращения'
            };

            // Генерируем примерный автоматический ответ
            $generatedResponse = null;
            if (rand(0, 1) && $status != 'new') { // 50% шанс для не новых лидов
                $generatedResponse = match($category) {
                    'запрос' => 'Благодарим за интерес к нашей компании! Информация о ' . ['продуктах', 'услугах', 'тарифах', 'условиях'][array_rand([0, 1, 2, 3])] . ' доступна на нашем сайте. С вами свяжется наш менеджер в ближайшее время для уточнения деталей.',
                    'жалоба' => 'Приносим извинения за доставленные неудобства. Мы уже работаем над решением проблемы. Пожалуйста, уточните детали вашего ' . ['заказа', 'обращения', 'ситуации'][array_rand([0, 1, 2])] . ' для более оперативного реагирования.',
                    'предложение' => 'Спасибо за ваше предложение! Мы внимательно изучим его и дадим обратную связь. Если потребуется дополнительная информация, наш специалист свяжется с вами.',
                    default => 'Благодарим за обращение в нашу компанию. Мы обработаем ваш запрос в ближайшее время.'
                };
            }

            // Создаем лид
            Lead::create([
                'company_id' => $company->id,
                'source' => $source,
                'name' => 'Тестовый клиент ' . $i,
                'email' => $hasEmail ? 'client' . $i . '@example.com' : null,
                'phone' => $hasPhone ? '+7' . rand(900, 999) . rand(1000000, 9999999) : null,
                'message' => $message,
                'custom_fields' => $customFields,
                'status' => $status,
                'category' => $category,
                'summary' => $summary,
                'generated_response' => $generatedResponse,
                'relevance_score' => $relevanceScore,
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
            ]);
        }

        $this->command->info('Создано 50 тестовых лидов для компании: ' . $company->name);
    }
}
