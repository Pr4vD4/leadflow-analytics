<section id="faq" class="py-24 bg-secondary-50 dark:bg-secondary-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl font-bold font-heading text-secondary-900 dark:text-white">
                Часто задаваемые вопросы
            </h2>
            <p class="mt-4 text-xl text-secondary-600 dark:text-secondary-300 max-w-3xl mx-auto">
                Ответы на популярные вопросы о LeadFlow Analytics
            </p>
        </div>

        <div class="max-w-3xl mx-auto divide-y divide-secondary-200 dark:divide-secondary-700" x-data="{active: null}">
            <!-- Вопрос 1 -->
            <div class="py-6" x-data="{id: 1}" :class="{'border-b': active !== 1}">
                <button @click="active !== 1 ? active = 1 : active = null" class="flex justify-between items-center w-full text-left" :aria-expanded="active === 1">
                    <h3 class="text-lg font-medium text-secondary-900 dark:text-white">
                        Что такое LeadFlow Analytics?
                    </h3>
                    <span class="ml-6 flex-shrink-0">
                        <svg class="h-5 w-5 text-primary-600 dark:text-primary-400 transition-transform duration-300" :class="{'transform rotate-180': active === 1}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>
                <div
                    x-show="active === 1"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-4">
                    <div class="mt-3 pr-12">
                        <p class="text-base text-secondary-600 dark:text-secondary-300">
                            LeadFlow Analytics — это комплексная платформа для управления лидами и аналитики, которая помогает бизнесам отслеживать взаимодействия с потенциальными клиентами, анализировать данные о продажах и принимать стратегические решения на основе точной информации.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Вопрос 2 -->
            <div class="py-6" x-data="{id: 2}" :class="{'border-b': active !== 2}">
                <button @click="active !== 2 ? active = 2 : active = null" class="flex justify-between items-center w-full text-left" :aria-expanded="active === 2">
                    <h3 class="text-lg font-medium text-secondary-900 dark:text-white">
                        Какие интеграции поддерживает платформа?
                    </h3>
                    <span class="ml-6 flex-shrink-0">
                        <svg class="h-5 w-5 text-primary-600 dark:text-primary-400 transition-transform duration-300" :class="{'transform rotate-180': active === 2}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>
                <div
                    x-show="active === 2"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-4">
                    <div class="mt-3 pr-12">
                        <p class="text-base text-secondary-600 dark:text-secondary-300">
                            LeadFlow Analytics интегрируется с широким спектром сервисов, включая социальные сети (Facebook, Instagram, LinkedIn), CRM-системы (Salesforce, HubSpot, Bitrix24), платформы электронной коммерции (Shopify, WooCommerce), рекламные площадки (Google Ads, Facebook Ads) и многие другие популярные инструменты.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Вопрос 3 -->
            <div class="py-6" x-data="{id: 3}" :class="{'border-b': active !== 3}">
                <button @click="active !== 3 ? active = 3 : active = null" class="flex justify-between items-center w-full text-left" :aria-expanded="active === 3">
                    <h3 class="text-lg font-medium text-secondary-900 dark:text-white">
                        Как LeadFlow Analytics помогает увеличить конверсию?
                    </h3>
                    <span class="ml-6 flex-shrink-0">
                        <svg class="h-5 w-5 text-primary-600 dark:text-primary-400 transition-transform duration-300" :class="{'transform rotate-180': active === 3}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>
                <div
                    x-show="active === 3"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-4">
                    <div class="mt-3 pr-12">
                        <p class="text-base text-secondary-600 dark:text-secondary-300">
                            Наша платформа предоставляет детальную аналитику по каждому этапу воронки продаж, помогая выявить узкие места и оптимизировать процессы. Вы можете сегментировать лидов по различным параметрам, автоматизировать коммуникацию и использовать предиктивную аналитику для определения наиболее перспективных потенциальных клиентов.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Вопрос 4 -->
            <div class="py-6" x-data="{id: 4}">
                <button @click="active !== 4 ? active = 4 : active = null" class="flex justify-between items-center w-full text-left" :aria-expanded="active === 4">
                    <h3 class="text-lg font-medium text-secondary-900 dark:text-white">
                        Какие тарифные планы предлагает LeadFlow Analytics?
                    </h3>
                    <span class="ml-6 flex-shrink-0">
                        <svg class="h-5 w-5 text-primary-600 dark:text-primary-400 transition-transform duration-300" :class="{'transform rotate-180': active === 4}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>
                <div
                    x-show="active === 4"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-4">
                    <div class="mt-3 pr-12">
                        <p class="text-base text-secondary-600 dark:text-secondary-300">
                            У нас есть несколько тарифных планов, адаптированных под разные потребности бизнеса — от стартапов до крупных предприятий. Базовый план включает основные функции управления лидами, а более продвинутые планы предлагают расширенную аналитику, автоматизацию и неограниченное количество пользователей. Мы также предлагаем индивидуальные корпоративные решения. Свяжитесь с нами для получения подробной информации о ценах.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 text-center">
            <a href="#contact" class="inline-flex items-center text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                У вас остались вопросы? Свяжитесь с нами
                <svg class="ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </div>
</section>
