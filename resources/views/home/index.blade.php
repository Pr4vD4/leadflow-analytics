@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <!-- Главный экран -->
    <header
        class="relative flex min-h-screen items-center overflow-hidden bg-secondary-50 dark:bg-secondary-900 pt-16">
        <!-- Фоновые частицы для главного экрана -->
        <x-particles id="header-particles" />

        <div class="relative z-10 mx-auto max-w-7xl px-4 py-16 sm:px-2 lg:px-4">
            <div class="grid grid-cols-1 items-center gap-6 lg:grid-cols-2">
                <div data-aos="fade-right" data-aos-duration="1000">
                    <h1
                        class="font-heading text-4xl font-bold tracking-tight text-secondary-900 dark:text-white md:text-5xl lg:text-6xl">
                        Управляйте лидами <span class="text-primary-600 dark:text-primary-400">эффективно</span>
                    </h1>
                    <p class="mt-6 max-w-lg text-xl text-secondary-600 dark:text-secondary-300">
                        LeadFlow Analytics превратит ваши данные в инсайты, упростит управление клиентами и повысит
                        конверсию.
                    </p>
                    <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                        <a href="#"
                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white transition duration-150 ease-in-out hover:bg-primary-700 focus:outline-none">
                            Начать бесплатно
                        </a>
                        <a href="#features"
                            class="inline-flex items-center justify-center rounded-md border border-secondary-300 bg-white px-6 py-3 text-base font-medium text-secondary-900 transition duration-150 ease-in-out hover:bg-secondary-50 focus:outline-none dark:border-secondary-700 dark:bg-secondary-800 dark:text-white dark:hover:bg-secondary-700">
                            Узнать больше
                        </a>
                    </div>
                </div>
                <div class="flex justify-center" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="relative h-[700px] w-full max-w-2xl">
                        <!-- Фоновые эффекты -->
                        <div
                            class="animate-blob absolute -left-6 -top-6 h-64 w-64 rounded-full bg-primary-200 opacity-70 mix-blend-multiply blur-xl filter dark:bg-primary-800/30 dark:mix-blend-overlay">
                        </div>
                        <div
                            class="animate-blob animation-delay-2000 absolute -bottom-8 -right-4 h-64 w-64 rounded-full bg-secondary-200 opacity-70 mix-blend-multiply blur-xl filter dark:bg-secondary-700/30 dark:mix-blend-overlay">
                        </div>
                        <div
                            class="animate-blob animation-delay-4000 absolute -bottom-20 left-20 h-64 w-64 rounded-full bg-primary-300 opacity-70 mix-blend-multiply blur-xl filter dark:bg-primary-600/30 dark:mix-blend-overlay">
                        </div>

                        <!-- Новая анимация с морфинг-фигурами вместо tsParticles -->
                        <div
                            class="relative z-10 h-full w-full overflow-hidden rounded-2xl bg-gray-50 shadow-xl dark:bg-secondary-800">
                            <!-- Компонент с анимацией потока данных -->
                            <x-morphing-shapes id="data-flow-animation" />

                            <!-- Текст аналитики -->
                            <div
                                class="absolute left-1/2 top-1/2 z-20 w-4/5 -translate-x-1/2 -translate-y-1/2 transform text-center transition-all duration-500 ease-out pointer-events-none">
                                <h3 class="mb-2 text-2xl font-bold text-secondary-800 dark:text-white">LeadFlow Analytics
                                </h3>
                                <p class="text-secondary-600 dark:text-secondary-300">Визуализация и анализ данных в
                                    реальном времени</p>
                            </div>

                            <!-- Стилизованные элементы диаграммы -->
                            <div
                                class="absolute bottom-6 left-1/2 z-20 flex -translate-x-1/2 transform items-end space-x-1 transition-all duration-500 ease-out pointer-events-none">
                                <div class="h-12 w-3 rounded-t-md bg-primary-500 opacity-80"></div>
                                <div class="h-16 w-3 rounded-t-md bg-primary-600 opacity-80"></div>
                                <div class="h-20 w-3 rounded-t-md bg-primary-700 opacity-80"></div>
                                <div class="h-14 w-3 rounded-t-md bg-primary-600 opacity-80"></div>
                                <div class="h-10 w-3 rounded-t-md bg-primary-500 opacity-80"></div>
                            </div>

                            <!-- Круговая диаграмма -->
                            <div class="absolute right-8 top-6 z-20 transition-all duration-500 ease-out pointer-events-none">
                                <div class="relative h-16 w-16">
                                    <svg viewBox="0 0 36 36" class="h-full w-full">
                                        <path class="fill-primary-200 dark:fill-primary-800" d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831" />
                                        <path class="fill-primary-500" stroke-dasharray="60, 100" d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    </svg>
                                    <div
                                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 transform text-xs font-semibold text-secondary-700 dark:text-white">
                                        60%</div>
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
    {{-- @include('home.partials.pricing') --}}
    @include('home.partials.faq')
    @include('home.partials.contact')
@endsection
