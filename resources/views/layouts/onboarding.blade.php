<!DOCTYPE html>
<html lang="ru" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LeadFlow Analytics')</title>

    <!-- Инлайн-скрипт для проверки темы и применения класса к документу -->
    <script>
        // Проверяем тему до загрузки страницы
        (function() {
            var darkMode = localStorage.getItem('darkMode') === 'true';
            if (darkMode) {
                // Добавляем класс к документу для глобального применения темной темы
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');

                // Добавим класс темного прелоадера через встроенный скрипт
                document.write('<style>.page-preloader{background:linear-gradient(135deg,#0F172A 0%,#18181B 46%,#1A1A2E 100%)}</style>');
            }
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- GSAP для анимаций -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <!-- Styles -->
    @stack('styles')
</head>

<body class="font-sans antialiased">
    <!-- Используем компонент прелоадера -->
    <x-preloader id="main-preloader" fullscreen="true" text="LeadFlow Analytics" />

    <div id="app" x-data="{
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
        }">
        <main class="content-container" id="content-container">
            @yield('content')
        </main>
    </div>

    <!-- Базовый скрипт для управления контентом -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Показываем контейнер контента
            setTimeout(() => {
                document.getElementById('content-container').classList.add('loaded');
            }, 100);
        });
    </script>

    <!-- Scripts -->
    @stack('scripts')
</body>

</html>
