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

                    // Наблюдение за изменением темы
                    const observer = new MutationObserver((mutations) => {
                        mutations.forEach((mutation) => {
                            if (mutation.attributeName === 'class') {
                                const darkMode = document.documentElement.classList.contains('dark');
                                tsParticles.setTheme('{{ $id }}', darkMode ? 'dark' : 'light');
                            }
                        });
                    });

                    observer.observe(document.documentElement, { attributes: true });
                });
            } else {
                console.error('tsParticles initialization function not found');
            }
        }
    }"
    class="absolute inset-0 w-full h-full z-0 pointer-events-none"
>
    <!-- Контейнер для tsParticles -->
    <div id="{{ $id }}" class="absolute inset-0 w-full h-full"></div>
</div>
