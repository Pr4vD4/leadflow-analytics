@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <!-- Главный экран -->
    <header class="pt-16 bg-gradient-to-br from-white to-primary-50 dark:from-secondary-900 dark:to-secondary-800 min-h-screen flex items-center relative overflow-hidden">
        <!-- Фоновые частицы для главного экрана -->
        <x-particles id="header-particles" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold font-heading tracking-tight text-secondary-900 dark:text-white">
                        Управляйте лидами <span class="text-primary-600 dark:text-primary-400">эффективно</span>
                    </h1>
                    <p class="mt-6 text-xl text-secondary-600 dark:text-secondary-300 max-w-lg">
                        LeadFlow Analytics превратит ваши данные в инсайты, упростит управление клиентами и повысит конверсию.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        <a href="#" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none transition duration-150 ease-in-out">
                            Начать бесплатно
                        </a>
                        <a href="#features" class="inline-flex items-center justify-center px-6 py-3 border border-secondary-300 dark:border-secondary-700 text-base font-medium rounded-md text-secondary-900 dark:text-white bg-white dark:bg-secondary-800 hover:bg-secondary-50 dark:hover:bg-secondary-700 focus:outline-none transition duration-150 ease-in-out">
                            Узнать больше
                        </a>
                    </div>
                </div>
                <div class="flex justify-center" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="relative w-full max-w-xl h-[500px]">
                        <!-- Фоновые эффекты -->
                        <div class="absolute -left-6 -top-6 w-64 h-64 bg-primary-200 dark:bg-primary-800/30 rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-xl opacity-70 animate-blob"></div>
                        <div class="absolute -right-4 -bottom-8 w-64 h-64 bg-secondary-200 dark:bg-secondary-700/30 rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                        <div class="absolute left-20 -bottom-20 w-64 h-64 bg-primary-300 dark:bg-primary-600/30 rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>

                        <!-- Новая анимация с морфинг-фигурами вместо tsParticles -->
                        <div class="relative w-full h-full rounded-2xl shadow-xl overflow-hidden bg-gray-50 dark:bg-secondary-800 z-10">
                            <!-- Контейнер для морфинг-фигур -->
                            <x-morphing-shapes id="morphing-shapes-container" />

                            <!-- Текст аналитики -->
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center z-20 w-4/5 transition-all duration-500 ease-out">
                                <h3 class="text-2xl font-bold text-secondary-800 dark:text-white mb-2">LeadFlow Analytics</h3>
                                <p class="text-secondary-600 dark:text-secondary-300">Визуализация и анализ данных в реальном времени</p>
                            </div>

                            <!-- Стилизованные элементы диаграммы -->
                            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-1 items-end z-20 transition-all duration-500 ease-out">
                                <div class="w-3 h-12 bg-primary-500 rounded-t-md opacity-80"></div>
                                <div class="w-3 h-16 bg-primary-600 rounded-t-md opacity-80"></div>
                                <div class="w-3 h-20 bg-primary-700 rounded-t-md opacity-80"></div>
                                <div class="w-3 h-14 bg-primary-600 rounded-t-md opacity-80"></div>
                                <div class="w-3 h-10 bg-primary-500 rounded-t-md opacity-80"></div>
                            </div>

                            <!-- Круговая диаграмма -->
                            <div class="absolute top-6 right-8 z-20 transition-all duration-500 ease-out">
                                <div class="relative w-16 h-16">
                                    <svg viewBox="0 0 36 36" class="w-full h-full">
                                        <path class="fill-primary-200 dark:fill-primary-800" d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="fill-primary-500" stroke-dasharray="60, 100" d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    </svg>
                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xs font-semibold text-secondary-700 dark:text-white">60%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    @include('home.partials.features')
    @include('home.partials.integration')
    @include('home.partials.analytics')
    @include('home.partials.testimonials')
    @include('home.partials.pricing')
    @include('home.partials.faq')
    @include('home.partials.contact')
@endsection
