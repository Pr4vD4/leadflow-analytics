<!-- Навигация -->
<nav class="bg-white dark:bg-secondary-800 shadow-sm fixed w-full z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" class="text-xl font-heading font-bold text-primary-600 dark:text-primary-400">LeadFlow Analytics</a>
                </div>
                <div class="max-xl:hidden xl:ml-6 xl:flex xl:space-x-8">
                    <a href="#features" class="border-transparent text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Возможности
                    </a>
                    <a href="#integration" class="border-transparent text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Интеграции
                    </a>
                    <a href="#analytics" class="border-transparent text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Аналитика
                    </a>
                    <a href="#testimonials" class="border-transparent text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Отзывы
                    </a>
                    <a href="#pricing" class="border-transparent text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Тарифы
                    </a>
                    <a href="#faq" class="border-transparent text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        FAQ
                    </a>
                    <a href="#contact" class="border-transparent text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Контакты
                    </a>
                </div>
            </div>
            <div class="flex items-center">
                <!-- Темная тема -->
                <button @click="toggleTheme()" class="p-2 rounded-full text-secondary-600 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-700 focus:outline-none">
                    <svg x-show="!darkMode" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" x-cloak class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                @if (Route::has('login'))
                    <div class="ml-4 flex items-center">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none">
                                Личный кабинет
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 text-sm font-medium">
                                Войти
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none">
                                    Регистрация
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif

                <!-- Мобильное меню -->
                <div class="-mr-2 flex items-center xl:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-md text-secondary-600 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-700 focus:outline-none">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Мобильное меню -->
    <div x-show="mobileMenuOpen" x-cloak class="xl:hidden" x-transition>
        <div class="pt-2 pb-3 space-y-1">
            <a href="#features" class="text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 block pl-3 pr-4 py-2 text-base font-medium">
                Возможности
            </a>
            <a href="#integration" class="text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 block pl-3 pr-4 py-2 text-base font-medium">
                Интеграции
            </a>
            <a href="#analytics" class="text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 block pl-3 pr-4 py-2 text-base font-medium">
                Аналитика
            </a>
            <a href="#testimonials" class="text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 block pl-3 pr-4 py-2 text-base font-medium">
                Отзывы
            </a>
            <a href="#pricing" class="text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 block pl-3 pr-4 py-2 text-base font-medium">
                Тарифы
            </a>
            <a href="#faq" class="text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 block pl-3 pr-4 py-2 text-base font-medium">
                FAQ
            </a>
            <a href="#contact" class="text-secondary-600 dark:text-secondary-300 hover:text-primary-600 dark:hover:text-primary-400 block pl-3 pr-4 py-2 text-base font-medium">
                Контакты
            </a>
        </div>
    </div>
</nav>
