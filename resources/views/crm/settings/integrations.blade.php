@extends('layouts.crm')

@section('title', 'Настройки интеграций')

@section('content')
    <div class="container mx-auto">
        <!-- Заголовок страницы -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Настройки компании</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Управление интеграциями с внешними сервисами</p>
        </div>

        <!-- Навигация по настройкам -->
        <div class="mb-6 border-b border-gray-200 dark:border-secondary-700">
            <nav class="flex -mb-px space-x-8">
                <a href="{{ route('crm.settings.general') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                    Общие настройки
                </a>
                <a href="{{ route('crm.settings.api') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                    API
                </a>
                <a href="{{ route('crm.settings.integrations') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-500">
                    Интеграции
                </a>
                <a href="{{ route('crm.settings.users') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                    Пользователи
                </a>
            </nav>
        </div>

        <!-- Сообщения об ошибках и уведомления -->
        @if(session('success'))
            <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 p-4 rounded-md mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Форма настроек интеграций -->
        <form action="{{ route('crm.settings.update-integrations') }}" method="POST">
            @csrf

            <!-- Интеграция с Telegram -->
            <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Telegram</h3>
                    <div class="flex items-center">
                        <span class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">Активно</span>
                        <label class="switch">
                            <input type="checkbox" name="telegram_enabled"
                                {{ isset($company->settings['integrations']['telegram']['enabled']) && $company->settings['integrations']['telegram']['enabled'] ? 'checked' : '' }}
                            >
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Настройте уведомления о новых заявках и изменении статусов в Telegram.
                    </p>

                    <div class="mb-4">
                        <label for="telegram_chat_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID чата</label>
                        <input type="text" name="telegram_chat_id" id="telegram_chat_id"
                            value="{{ isset($company->settings['integrations']['telegram']['chat_id']) ? $company->settings['integrations']['telegram']['chat_id'] : '' }}"
                            class="focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white rounded-md">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Чтобы получить ID чата, начните диалог с нашим ботом <a href="https://t.me/leadflow_bot" target="_blank" class="text-primary-600 dark:text-primary-500 hover:underline">@leadflow_bot</a> и отправьте команду /start.
                        </p>
                        @error('telegram_chat_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Интеграция с Bitrix24 -->
            <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Bitrix24</h3>
                    <div class="flex items-center">
                        <span class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">Активно</span>
                        <label class="switch">
                            <input type="checkbox" name="bitrix24_enabled"
                                {{ isset($company->settings['integrations']['bitrix24']['enabled']) && $company->settings['integrations']['bitrix24']['enabled'] ? 'checked' : '' }}
                            >
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Настройте синхронизацию заявок с вашим порталом Bitrix24.
                    </p>

                    <div class="mb-4">
                        <label for="bitrix24_webhook_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL вебхука</label>
                        <input type="text" name="bitrix24_webhook_url" id="bitrix24_webhook_url"
                            value="{{ isset($company->settings['integrations']['bitrix24']['webhook_url']) ? $company->settings['integrations']['bitrix24']['webhook_url'] : '' }}"
                            class="focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white rounded-md">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Формат: https://your-domain.bitrix24.ru/rest/1/your-webhook-code/
                        </p>
                        @error('bitrix24_webhook_url')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    /* Стили для переключателя */
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 22px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #4f46e5;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #4f46e5;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(18px);
        -ms-transform: translateX(18px);
        transform: translateX(18px);
    }

    html.dark .slider {
        background-color: #4B5563;
    }

    html.dark input:checked + .slider {
        background-color: #4f46e5;
    }
</style>
@endpush
