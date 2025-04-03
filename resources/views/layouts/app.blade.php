<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'LeadFlow Analytics') }} - @yield('title', 'Управление лидами')</title>

        <!-- Предотвращение мигания при загрузке темы -->
        <script>
            // Проверяем тему до загрузки DOM
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
        </script>

        <!-- Прелоадер -->
        <style>
            .preloader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 9999;
                transition: opacity 0.3s;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .preloader.light {
                background-color: #f9fafb; /* bg-gray-50 */
            }
            .preloader.dark {
                background-color: #0f172a; /* bg-secondary-900 */
            }
            .preloader .spinner {
                width: 50px;
                height: 50px;
                border: 5px solid rgba(0, 0, 0, 0.1);
                border-radius: 50%;
                border-top-color: #4f46e5; /* indigo-600 */
                animation: spin 1s ease-in-out infinite;
            }
            .dark .preloader .spinner {
                border-color: rgba(255, 255, 255, 0.1);
                border-top-color: #4f46e5;
            }
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            .hidden {
                opacity: 0;
                pointer-events: none;
            }
        </style>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- AOS CSS -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

        <style>
            [x-cloak] { display: none !important; }
        </style>

        @stack('styles')
    </head>
    <body class="antialiased bg-gray-50 dark:bg-secondary-900">
        <!-- Прелоадер -->
        <div id="preloader" class="preloader">
            <div class="spinner"></div>
        </div>

        <div x-data="{
                mobileMenuOpen: false,
                darkMode: localStorage.getItem('darkMode') === 'true',
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
             x-init="$watch('darkMode', val => {
                localStorage.setItem('darkMode', val);
                if (val) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.classList.remove('light');
                }
                else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.classList.add('light');
                }
             })">

            @include('components.header')

            <main class="overflow-x-hidden">
                @yield('content')
            </main>

            @include('components.footer')
        </div>

        <!-- Alpine.js -->
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <!-- AOS JS -->
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            // Настройка прелоадера
            document.addEventListener('DOMContentLoaded', function() {
                // Применение темы к прелоадеру
                const preloader = document.getElementById('preloader');
                if (localStorage.getItem('darkMode') === 'true') {
                    preloader.classList.add('dark');
                } else {
                    preloader.classList.add('light');
                }

                // Инициализация AOS
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true,
                    mirror: false
                });

                // Проверяем текущую тему из localStorage при загрузке
                const darkMode = localStorage.getItem('darkMode') === 'true';
                if (darkMode) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.classList.remove('light');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.classList.add('light');
                }

                // Скрываем прелоадер после загрузки контента
                setTimeout(function() {
                    preloader.classList.add('hidden');
                }, 300);
            });
        </script>

        @stack('scripts')
    </body>
</html>
