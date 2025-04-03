@extends('layouts.onboarding')

@section('title', 'Примеры прелоадера')

@section('content')
<div class="p-8">
    <h1 class="text-xl font-bold mb-6">Примеры использования компонента прелоадера</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Пример 1: Блочный прелоадер -->
        <div class="p-6 bg-white shadow-md rounded-lg">
            <h2 class="text-lg font-semibold mb-4">1. Прелоадер в блоке</h2>
            <div class="relative h-64 bg-gray-100 rounded-md overflow-hidden" id="example-block-1">
                <x-preloader :fullscreen="false" text="Загрузка..." id="block-preloader-1" size="small" />
                <div class="content p-4 hidden">
                    <p>Это контент блока, который загружается с задержкой.</p>
                    <p class="mt-4">Во время загрузки показывается прелоадер.</p>
                </div>
            </div>
            <button class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md"
                    onclick="loadBlockContent(1)">
                Загрузить контент
            </button>
        </div>

        <!-- Пример 2: Маленький прелоадер без текста -->
        <div class="p-6 bg-white shadow-md rounded-lg">
            <h2 class="text-lg font-semibold mb-4">2. Маленький прелоадер без текста</h2>
            <div class="relative h-64 bg-gray-100 rounded-md overflow-hidden" id="example-block-2">
                <x-preloader :fullscreen="false" text="" id="block-preloader-2" size="small" />
                <div class="content p-4 hidden">
                    <p>Содержимое с маленьким прелоадером без текста.</p>
                </div>
            </div>
            <button class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md"
                    onclick="loadBlockContent(2)">
                Загрузить контент
            </button>
        </div>

        <!-- Пример 3: Динамически созданный прелоадер -->
        <div class="p-6 bg-white shadow-md rounded-lg">
            <h2 class="text-lg font-semibold mb-4">3. Динамический прелоадер через JavaScript</h2>
            <div class="relative h-64 bg-gray-100 rounded-md overflow-hidden" id="example-block-3">
                <div class="content p-4">
                    <p>Нажмите кнопку для создания динамического прелоадера</p>
                </div>
            </div>
            <button class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md"
                    onclick="createDynamicPreloader()">
                Создать прелоадер
            </button>
        </div>

        <!-- Пример 4: Большой прелоадер -->
        <div class="p-6 bg-white shadow-md rounded-lg">
            <h2 class="text-lg font-semibold mb-4">4. Большой прелоадер</h2>
            <div class="relative h-64 bg-gray-100 rounded-md overflow-hidden" id="example-block-4">
                <x-preloader :fullscreen="false" text="Большой" id="block-preloader-4" size="large" />
                <div class="content p-4 hidden">
                    <p>Блок с большим прелоадером</p>
                </div>
            </div>
            <button class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md"
                    onclick="loadBlockContent(4)">
                Загрузить контент
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function loadBlockContent(blockId) {
        // Показываем прелоадер (в случае, если он был скрыт)
        Preloader.show('block-preloader-' + blockId);

        // Симулируем задержку загрузки
        setTimeout(function() {
            // Скрываем прелоадер
            Preloader.hide('block-preloader-' + blockId);

            // Показываем контент
            const contentBlock = document.querySelector('#example-block-' + blockId + ' .content');
            if (contentBlock) {
                contentBlock.classList.remove('hidden');
            }
        }, 2000); // Задержка 2 секунды для демонстрации
    }

    function createDynamicPreloader() {
        // Создаем прелоадер в блоке
        const containerId = 'example-block-3';
        const preloaderId = Preloader.createInElement(containerId, {
            text: 'Динамический',
            size: 'small'
        });

        // Скрываем контент
        const contentBlock = document.querySelector('#' + containerId + ' .content');
        if (contentBlock) {
            contentBlock.classList.add('hidden');
        }

        // Симулируем задержку загрузки
        setTimeout(function() {
            // Скрываем прелоадер
            Preloader.hide(preloaderId);

            // Показываем контент
            if (contentBlock) {
                contentBlock.classList.remove('hidden');
            }
        }, 2000); // Задержка 2 секунды для демонстрации
    }
</script>
@endpush
