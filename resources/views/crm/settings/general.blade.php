@extends('layouts.crm')

@section('title', 'Общие настройки')

@section('content')
    <div class="container mx-auto">
        <!-- Заголовок страницы -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Настройки компании</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Управление основной информацией о компании</p>
        </div>

        <!-- Навигация по настройкам -->
        <div class="mb-6 border-b border-gray-200 dark:border-secondary-700">
            <nav class="flex -mb-px space-x-8">
                <a href="{{ route('crm.settings.general') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-500">
                    Общие настройки
                </a>
                <a href="{{ route('crm.settings.api') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                    API
                </a>
                <a href="{{ route('crm.settings.integrations') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
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

        <!-- Форма настроек компании -->
        <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Основная информация</h3>
            </div>
            <div class="px-6 py-4">
                <form action="{{ route('crm.settings.update-general') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Название компании -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Название компании</label>
                            <input type="text" name="name" id="name"
                                value="{{ old('name', $company->name) }}"
                                class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white rounded-md"
                                required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" name="email" id="email"
                                value="{{ old('email', $company->email) }}"
                                class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white rounded-md">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Телефон -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Телефон</label>
                            <input type="text" name="phone" id="phone"
                                value="{{ old('phone', $company->phone) }}"
                                class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white rounded-md">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Адрес -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Адрес</label>
                            <input type="text" name="address" id="address"
                                value="{{ old('address', $company->address) }}"
                                class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white rounded-md">
                            @error('address')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
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
        </div>
    </div>
@endsection
