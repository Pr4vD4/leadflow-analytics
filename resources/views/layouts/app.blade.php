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
        <!-- Использование нового компонента прелоадера -->
        <x-preloader id="main-preloader" fullscreen="true" text="LeadFlow Analytics" />

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

            <main class="overflow-x-hidden content-container" id="content-container">
                @yield('content')
            </main>

            @include('components.footer')
        </div>

        <!-- Alpine.js -->
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <!-- AOS JS -->
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Инициализация AOS
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true,
                    mirror: false
                });

                // Показываем контейнер контента
                setTimeout(() => {
                    document.getElementById('content-container').classList.add('loaded');
                }, 100);
            });
        </script>

        @stack('scripts')
    </body>
</html>
