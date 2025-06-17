@props(['id'])

<div
    x-data="{
        init() {
            // Инициализация tsParticles
            if (typeof window.initParticles === 'function') {
                window.initParticles('{{ $id }}').then(() => {
                    // Установка темы в зависимости от текущей темы сайта
                    const darkMode = document.documentElement.classList.contains('dark');
                    tsParticles.setTheme('{{ $id }}', darkMode ? 'dark' : 'light');

                    // Наблюдение за изменением темы на HTML элементе
                    const observer = new MutationObserver((mutations) => {
                        mutations.forEach((mutation) => {
                            if (mutation.attributeName === 'class') {
                                const isDark = document.documentElement.classList.contains('dark');
                                tsParticles.setTheme('{{ $id }}', isDark ? 'dark' : 'light');
                            }
                        });
                    });

                    // Отслеживать изменения класса на HTML-элементе
                    observer.observe(document.documentElement, { attributes: true });

                    // Также проверять изменения через localStorage
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'darkMode') {
                            const isDark = e.newValue === 'true';
                            tsParticles.setTheme('{{ $id }}', isDark ? 'dark' : 'light');
                        }
                    });
                });
            } else {
                console.error('tsParticles initialization function not found');
            }
        }
    }"
    class="absolute inset-0 w-full h-full z-0 pointer-events-none"
>
    <!-- Контейнер для tsParticles -->
    <div id="{{ $id }}" class="absolute inset-0 w-full h-full bg-transparent"></div>
</div>
