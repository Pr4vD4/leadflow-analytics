@props([
    'fullscreen' => true,
    'text' => 'LeadFlow Analytics',
    'id' => 'preloader-' . uniqid(),
    'size' => 'default', // default, small, large
    'showNow' => true,   // определяет, показывать ли прелоадер сразу
])

@php
    $sizeClass = match($size) {
        'small' => 'preloader-small',
        'large' => 'preloader-large',
        default => '',
    };

    $containerClass = $fullscreen
        ? 'page-preloader'
        : 'block-preloader';

    $displayStyle = $showNow ? '' : 'display: none;';
@endphp

<div id="{{ $id }}" class="{{ $containerClass }} {{ $sizeClass }}" style="{{ $displayStyle }}">
    <div class="preloader-content">
        <div class="preloader-spinner">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
        @if($text)
            <div class="preloader-text">{{ $text }}</div>
        @endif
    </div>
</div>

@once
<style>
    /* Общие стили для прелоадера */
    .page-preloader,
    .block-preloader {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);
        background-size: 400% 400%;
        animation: gradient 15s ease infinite;
        transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    /* Полноэкранный прелоадер */
    .page-preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
    }

    /* Прелоадер внутри блока */
    .block-preloader {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
        border-radius: inherit;
    }

    /* Варианты размеров */
    .preloader-small .preloader-spinner {
        width: 40px;
        height: 40px;
    }

    .preloader-small .preloader-spinner .dot {
        width: 8px;
        height: 8px;
    }

    .preloader-small .preloader-text {
        font-size: 14px;
    }

    .preloader-large .preloader-spinner {
        width: 120px;
        height: 120px;
    }

    .preloader-large .preloader-spinner .dot {
        width: 24px;
        height: 24px;
    }

    .preloader-large .preloader-text {
        font-size: 24px;
    }

    /* Контент прелоадера */
    .preloader-content {
        text-align: center;
    }

    .preloader-spinner {
        position: relative;
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
    }

    .preloader-spinner .dot {
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: white;
        animation: preloader-dot-animation 2s infinite ease-in-out;
    }

    .preloader-spinner .dot:nth-child(1) {
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        animation-delay: -0.9s;
    }

    .preloader-spinner .dot:nth-child(2) {
        top: 18%;
        right: 18%;
        animation-delay: -0.8s;
    }

    .preloader-spinner .dot:nth-child(3) {
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        animation-delay: -0.7s;
    }

    .preloader-spinner .dot:nth-child(4) {
        bottom: 18%;
        right: 18%;
        animation-delay: -0.6s;
    }

    .preloader-spinner .dot:nth-child(5) {
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        animation-delay: -0.5s;
    }

    .preloader-spinner .dot:nth-child(6) {
        bottom: 18%;
        left: 18%;
        animation-delay: -0.4s;
    }

    .preloader-spinner .dot:nth-child(7) {
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        animation-delay: -0.3s;
    }

    .preloader-spinner .dot:nth-child(8) {
        top: 18%;
        left: 18%;
        animation-delay: -0.2s;
    }

    @keyframes preloader-dot-animation {
        0%, 100% {
            transform: scale(0.6);
            opacity: 0.6;
        }
        50% {
            transform: scale(1.2);
            opacity: 1;
        }
    }

    .preloader-text {
        color: white;
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 1px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Темная тема для прелоадера */
    .dark-mode .page-preloader,
    .dark-mode .block-preloader,
    .dark .page-preloader,
    .dark .block-preloader,
    [data-theme="dark"] .page-preloader,
    [data-theme="dark"] .block-preloader {
        background: linear-gradient(135deg, #0F172A 0%, #18181B 46%, #1A1A2E 100%);
    }

    /* Скрытие прелоадера */
    .preloader-hide {
        opacity: 0;
        visibility: hidden;
    }
</style>

<script>
    window.Preloader = {
        // Скрытие прелоадера
        hide: function(id, removeAfter = true) {
            const preloader = document.getElementById(id);
            if (!preloader) return;

            preloader.classList.add('preloader-hide');

            if (removeAfter) {
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 600);
            }
        },

        // Показ прелоадера
        show: function(id) {
            const preloader = document.getElementById(id);
            if (!preloader) return;

            preloader.style.display = 'flex';
            preloader.classList.remove('preloader-hide');
        },

        // Создание нового прелоадера внутри элемента
        createInElement: function(elementId, options = {}) {
            const container = document.getElementById(elementId);
            if (!container) return null;

            // Применяем настройки по умолчанию
            const settings = Object.assign({
                fullscreen: false,
                text: '',
                size: 'small',
                showNow: true
            }, options);

            // Создаем уникальный ID
            const preloaderId = 'preloader-' + Math.random().toString(36).substring(2, 9);

            // Позиционирование родительского элемента
            if (window.getComputedStyle(container).position === 'static') {
                container.style.position = 'relative';
            }

            // Создаем HTML для прелоадера
            const html = `
                <div id="${preloaderId}" class="block-preloader ${settings.size === 'small' ? 'preloader-small' : ''}" style="${!settings.showNow ? 'display: none;' : ''}">
                    <div class="preloader-content">
                        <div class="preloader-spinner">
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                        </div>
                        ${settings.text ? `<div class="preloader-text">${settings.text}</div>` : ''}
                    </div>
                </div>
            `;

            // Добавляем прелоадер в контейнер
            container.insertAdjacentHTML('beforeend', html);

            return preloaderId;
        }
    };

    // Скрытие page-preloader при загрузке страницы
    window.addEventListener('load', function() {
        // Находим все page-preloader на странице
        const pagePreloaders = document.querySelectorAll('.page-preloader');

        setTimeout(function() {
            pagePreloaders.forEach(preloader => {
                if (preloader && preloader.id) {
                    window.Preloader.hide(preloader.id);
                }
            });
        }, 800);
    });
</script>
@endonce
