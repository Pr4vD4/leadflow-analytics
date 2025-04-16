<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'LeadFlow Analytics') }} - @yield('title', 'CRM Система')</title>

        <!-- Предотвращение мигания при загрузке темы -->
        <script>
            // Проверяем тему до загрузки DOM
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            [x-cloak] { display: none !important; }
        </style>

        @stack('styles')
    </head>
    <body class="antialiased bg-gray-50 dark:bg-secondary-900">
        <!-- Прелоадер -->
        <x-preloader id="main-preloader" fullscreen="true" text="LeadFlow Analytics" />

        <div x-data="{
                sidebarOpen: true,
                darkMode: localStorage.getItem('darkMode') === 'true',
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                    localStorage.setItem('sidebarOpen', this.sidebarOpen);
                },
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                        document.documentElement.classList.remove('light');
                    } else {
                        document.documentElement.classList.remove('dark');
                        document.documentElement.classList.add('light');
                    }
                }
             }"
             x-init="
                $watch('darkMode', val => {
                    localStorage.setItem('darkMode', val);
                    if (val) {
                        document.documentElement.classList.add('dark');
                        document.documentElement.classList.remove('light');
                    }
                    else {
                        document.documentElement.classList.remove('dark');
                        document.documentElement.classList.add('light');
                    }
                });

                if (localStorage.getItem('sidebarOpen') === 'false') {
                    sidebarOpen = false;
                }
             ">

            <!-- Боковое меню -->
            <div class="flex h-screen overflow-hidden">
                <!-- Сайдбар -->
                <div x-show="sidebarOpen"
                     class="w-64 bg-white dark:bg-secondary-800 border-r border-gray-200 dark:border-secondary-700 fixed inset-y-0 left-0 z-20 transform transition-all duration-300"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full">

                    <!-- Логотип -->
                    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-secondary-700">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <span class="text-xl font-bold text-primary-600 dark:text-primary-500">LeadFlow</span>
                        </a>
                        <button @click="toggleSidebar" class="p-1 rounded-md lg:hidden">
                            <i class="fas fa-times text-gray-500 dark:text-gray-400"></i>
                        </button>
                    </div>

                    <!-- Навигация -->
                    <nav class="px-4 py-4">
                        <div class="space-y-1">
                            <a href="{{ route('crm.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('crm.dashboard') ? 'bg-primary-100 text-primary-600 dark:bg-primary-900 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-secondary-700' }}">
                                <i class="fas fa-chart-pie mr-3 text-gray-500 dark:text-gray-400"></i>
                                Дашборд
                            </a>
                            <a href="{{ route('crm.leads.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('crm.leads.*') ? 'bg-primary-100 text-primary-600 dark:bg-primary-900 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-secondary-700' }}">
                                <i class="fas fa-clipboard-list mr-3 text-gray-500 dark:text-gray-400"></i>
                                Заявки
                            </a>
                            <a href="{{ route('crm.analytics.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('crm.analytics.*') ? 'bg-primary-100 text-primary-600 dark:bg-primary-900 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-secondary-700' }}">
                                <i class="fas fa-chart-line mr-3 text-gray-500 dark:text-gray-400"></i>
                                Аналитика
                            </a>
                            <a href="{{ route('crm.settings.general') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('crm.settings.*') ? 'bg-primary-100 text-primary-600 dark:bg-primary-900 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-secondary-700' }}">
                                <i class="fas fa-cog mr-3 text-gray-500 dark:text-gray-400"></i>
                                Настройки
                            </a>
                        </div>
                    </nav>
                </div>

                <!-- Основной контент -->
                <div class="flex flex-col flex-1 overflow-hidden" :class="{'lg:pl-64': sidebarOpen}">
                    <!-- Верхняя панель -->
                    <header class="bg-white dark:bg-secondary-800 border-b border-gray-200 dark:border-secondary-700 h-16 flex items-center justify-between px-4 lg:px-6">
                        <button @click="toggleSidebar" class="p-1 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-bars text-gray-500 dark:text-gray-400"></i>
                        </button>

                        <div class="flex items-center">
                            <!-- Переключатель темы -->
                            <button @click="toggleTheme" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none">
                                <i x-show="!darkMode" class="fas fa-moon"></i>
                                <i x-show="darkMode" class="fas fa-sun"></i>
                            </button>

                            <!-- Профиль пользователя -->
                            <div class="relative ml-4" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&color=7F9CF5&background=EBF4FF" alt="{{ auth()->user()->name }}">
                                    <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->name }}</span>
                                </button>

                                <div x-show="open"
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-secondary-800 rounded-md shadow-lg py-1 z-50"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-secondary-700">Профиль</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-secondary-700">Настройки</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-secondary-700">
                                            Выйти
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </header>

                    <!-- Основной контент -->
                    <main class="flex-1 overflow-y-auto p-4 lg:p-6 bg-gray-50 dark:bg-secondary-900">
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>

        <!-- Alpine.js -->
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Показываем контейнер контента
                setTimeout(() => {
                    document.getElementById('main-preloader').classList.add('hidden');
                }, 500);
            });
        </script>

        @stack('scripts')
    </body>
</html>
