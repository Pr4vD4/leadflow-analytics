@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <!-- Главный экран -->
    <header class="pt-16 bg-gradient-to-br from-white to-primary-50 dark:from-secondary-900 dark:to-secondary-800 min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
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
                    <div class="relative">
                        <div class="absolute -left-6 -top-6 w-64 h-64 bg-primary-200 dark:bg-primary-800/30 rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-xl opacity-70 animate-blob"></div>
                        <div class="absolute -right-4 -bottom-8 w-64 h-64 bg-secondary-200 dark:bg-secondary-700/30 rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                        <div class="absolute left-20 -bottom-20 w-64 h-64 bg-primary-300 dark:bg-primary-600/30 rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
                        <img class="relative z-10 rounded-2xl shadow-xl max-w-md" src="https://via.placeholder.com/600x400?text=LeadFlow+Analytics+Dashboard" alt="Дашборд аналитики">
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
