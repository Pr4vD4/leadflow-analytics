@extends('layouts.onboarding')

@section('title', 'Создание компании')

@push('styles')
<style>
    :root {
        --primary-color: #4F46E5;
        --primary-hover: #4338CA;
        --primary-light: rgba(79, 70, 229, 0.1);
        --text-color: #1F2937;
        --text-light: #6B7280;
        --text-input: #4B5563;
        --bg-input: #ffffff;
        --bg-form: rgba(255, 255, 255, 0.8);
        --bg-gradient-1: #4158D0;
        --bg-gradient-2: #C850C0;
        --bg-gradient-3: #FFCC70;
        --border-color: #E5E7EB;
        --success-color: #22C55E;
        --error-color: #EF4444;
        --shadow-color: rgba(0, 0, 0, 0.1);
        --backdrop-blur: 16px;
    }

    .dark-mode {
        --primary-color: #6366F1;
        --primary-hover: #818CF8;
        --primary-light: rgba(99, 102, 241, 0.2);
        --text-color: #F9FAFB;
        --text-light: #D1D5DB;
        --text-input: #E5E7EB;
        --bg-input: #1F2937;
        --bg-form: rgba(31, 41, 55, 0.9);
        --bg-gradient-1: #0F172A;
        --bg-gradient-2: #18181B;
        --bg-gradient-3: #1A1A2E;
        --border-color: #374151;
        --success-color: #10B981;
        --error-color: #EF4444;
        --shadow-color: rgba(0, 0, 0, 0.3);
        --backdrop-blur: 20px;
    }

    .onboarding-bg {
        background: linear-gradient(135deg, var(--bg-gradient-1) 0%, var(--bg-gradient-2) 46%, var(--bg-gradient-3) 100%);
        background-size: 400% 400%;
        animation: gradient 15s ease infinite;
        transition: all 0.3s ease;
    }

    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .form-container {
        backdrop-filter: blur(var(--backdrop-blur)) saturate(180%);
        background-color: var(--bg-form);
        border-radius: 16px;
        border: 1px solid rgba(209, 213, 219, 0.3);
        box-shadow: 0 10px 25px var(--shadow-color);
        overflow: hidden;
        transition: all 0.5s ease;
    }

    .form-step {
        position: relative;
        width: 100%;
        transition: transform 0.5s ease, opacity 0.5s ease;
        display: none;
    }

    .form-step.active {
        display: block;
        transform: translateX(0);
        opacity: 1;
        z-index: 1;
    }

    .form-step.before {
        display: none;
        transform: translateX(-100%);
        opacity: 0;
        z-index: 0;
    }

    .form-step.after {
        display: none;
        transform: translateX(100%);
        opacity: 0;
        z-index: 0;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
        transition: all 0.3s ease;
        border-radius: 10px;
        font-weight: 500;
        letter-spacing: 0.025em;
        padding: 10px 16px;
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.15);
        border: none;
        outline: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(79, 70, 229, 0.2);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background-color: transparent;
        color: var(--primary-color);
        border: 1.5px solid var(--primary-color);
        transition: all 0.3s ease;
        border-radius: 10px;
        font-weight: 500;
        letter-spacing: 0.025em;
        padding: 9px 15px;
    }

    .btn-secondary:hover {
        background-color: var(--primary-light);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.1);
    }

    .btn-back {
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 100;
        color: white;
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 50%;
        padding: 10px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }

    .btn-back:hover {
        background-color: rgba(0, 0, 0, 0.4);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-back:active {
        transform: translateY(0);
    }

    .tab-selector {
        position: relative;
        display: flex;
        margin-bottom: 32px;
        overflow: hidden;
        border-radius: 12px;
        background-color: rgba(229, 231, 235, 0.3);
        padding: 3px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .tab-selector .tab {
        flex: 1;
        text-align: center;
        padding: 12px 16px;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
        cursor: pointer;
        color: var(--text-color);
        border-radius: 10px;
    }

    .tab-selector .tab:hover:not(.active) {
        background-color: rgba(229, 231, 235, 0.5);
    }

    .tab-selector .tab.active {
        color: white;
    }

    .tab-selector .tab-bg {
        position: absolute;
        height: calc(100% - 6px);
        width: calc(50% - 6px);
        background-color: var(--primary-color);
        border-radius: 10px;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        top: 3px;
        left: 3px;
        z-index: 0;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
    }

    .tab-selector.join .tab-bg {
        transform: translateX(calc(100% + 3px));
    }

    .floating-label {
        position: relative;
        margin-bottom: 28px;
    }

    .floating-label label {
        position: absolute;
        top: 0;
        left: 0;
        padding: 18px 12px;
        pointer-events: none;
        transition: 0.3s ease all;
        color: var(--text-light);
        font-size: 16px;
    }

    .floating-label input:focus ~ label,
    .floating-label input:not(:placeholder-shown) ~ label,
    .floating-label textarea:focus ~ label,
    .floating-label textarea:not(:placeholder-shown) ~ label {
        transform: translateY(-24px) scale(0.85);
        color: var(--primary-color);
        padding: 0;
        font-weight: 500;
    }

    .floating-label input,
    .floating-label textarea {
        font-size: 16px;
        padding: 18px 16px;
        display: block;
        width: 100%;
        border: none;
        border-radius: 10px;
        border: 1.5px solid var(--border-color);
        background-color: var(--bg-input);
        color: var(--text-input);
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
    }

    .floating-label input:focus,
    .floating-label textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    .pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Анимация проверки вводимого контента */
    .check-animation {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: var(--success-color);
        margin: 0 auto;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: scale(0);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
        margin-top: 1rem;
    }

    .check-animation.active {
        display: flex;
        opacity: 1;
        transform: scale(1);
    }

    .check-animation svg {
        width: 32px;
        height: 32px;
        color: white;
        stroke-width: 3;
    }

    /* Переключатель темы */
    .theme-toggle {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 100;
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 50%;
        padding: 8px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white;
    }

    .theme-toggle:hover {
        background-color: rgba(0, 0, 0, 0.4);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .theme-toggle svg {
        width: 22px;
        height: 22px;
        transition: all 0.5s ease;
    }

    .theme-toggle .moon {
        display: block;
    }

    .theme-toggle .sun {
        display: none;
    }

    .dark-mode .theme-toggle .moon {
        display: none;
    }

    .dark-mode .theme-toggle .sun {
        display: block;
    }

    .h2-title {
        color: var(--text-color);
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-align: center;
        font-size: 1.5rem;
    }

    .text-hint {
        color: var(--text-light);
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen onboarding-bg flex items-center justify-center p-4 relative" id="app-container">
    <!-- Кнопка возврата на главную -->
    <a href="{{ route('home') }}" class="btn-back">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
    </a>

    <!-- Переключатель темы -->
    <div class="theme-toggle" onclick="toggleDarkMode()">
        <svg xmlns="http://www.w3.org/2000/svg" class="moon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" class="sun" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    </div>

    <!-- Форма в красивом контейнере -->
    <div class="max-w-md w-full form-container p-6 md:p-8 relative" x-data="onboardingApp()">
        <!-- Главный переключатель: создать или присоединиться -->
        <div class="tab-selector" :class="{'join': mode === 'join'}">
            <div class="tab" :class="{'active': mode === 'create'}" @click="setMode('create')">Создать компанию</div>
            <div class="tab" :class="{'active': mode === 'join'}" @click="setMode('join')">Присоединиться</div>
            <div class="tab-bg"></div>
        </div>

        <!-- Создание компании - шаги -->
        <div x-show="mode === 'create'">
            <!-- Шаг 1: Название компании -->
            <div class="form-step" :class="getStepClass(1)" x-show="currentStep === 1">
                <h2 class="h2-title">Как называется ваша компания?</h2>
                <div class="floating-label">
                    <input type="text" id="company_name" x-model="company.name" @keyup.enter="validateStep(1)" placeholder=" " required>
                    <label for="company_name">Название компании</label>
                </div>
                <div class="text-center">
                    <button type="button" class="btn-primary px-6 py-3 rounded-md text-white font-medium mt-4" @click="validateStep(1)">
                        Продолжить
                    </button>
                </div>

                <!-- Анимация проверки -->
                <div class="check-animation mt-4" :class="{'active': showCheckAnimation}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
            </div>

            <!-- Шаг 2: Контактная информация -->
            <div class="form-step" :class="getStepClass(2)" x-show="currentStep === 2">
                <h2 class="h2-title">Контактная информация</h2>
                <div class="floating-label">
                    <input type="email" id="company_email" x-model="company.email" placeholder=" " required>
                    <label for="company_email">Email компании</label>
                </div>
                <div class="floating-label">
                    <input type="tel" id="company_phone" x-model="company.phone" placeholder=" ">
                    <label for="company_phone">Телефон компании</label>
                </div>
                <div class="flex justify-between mt-4 gap-4">
                    <button type="button" class="btn-secondary w-1/2 py-3 rounded-md font-medium" @click="prevStep()">
                        Назад
                    </button>
                    <button type="button" class="btn-primary w-1/2 py-3 rounded-md text-white font-medium" @click="validateStep(2)">
                        Продолжить
                    </button>
                </div>
            </div>

            <!-- Шаг 3: Описание компании -->
            <div class="form-step" :class="getStepClass(3)" x-show="currentStep === 3">
                <h2 class="h2-title">Расскажите о вашей компании</h2>
                <div class="floating-label">
                    <textarea id="company_description" x-model="company.description" rows="4" placeholder=" "></textarea>
                    <label for="company_description">Краткое описание</label>
                </div>
                <div class="flex justify-between mt-4 gap-4">
                    <button type="button" class="btn-secondary w-1/2 py-3 rounded-md font-medium" @click="prevStep()">
                        Назад
                    </button>
                    <button type="button" class="btn-primary w-1/2 py-3 rounded-md text-white font-medium" @click="submitForm()">
                        Создать компанию
                    </button>
                </div>
            </div>
        </div>

        <!-- Присоединение к компании -->
        <div x-show="mode === 'join'">
            <div class="form-step active">
                <h2 class="h2-title">Введите код приглашения</h2>
                <div class="floating-label">
                    <input type="text" id="invitation_code" x-model="invitationCode" placeholder=" " required>
                    <label for="invitation_code">Код приглашения</label>
                </div>
                <p class="text-hint">
                    Код приглашения можно получить у администратора компании
                </p>
                <div class="text-center">
                    <button type="button" class="btn-primary px-8 py-3 rounded-md text-white font-medium" @click="joinCompany()">
                        Присоединиться
                    </button>
                </div>
            </div>
        </div>

        <!-- Скрытая форма для отправки данных -->
        <form id="create-company-form" method="POST" action="{{ route('companies.store') }}" class="hidden">
            @csrf
            <input type="hidden" name="name" :value="company.name">
            <input type="hidden" name="email" :value="company.email">
            <input type="hidden" name="phone" :value="company.phone">
            <input type="hidden" name="description" :value="company.description">
        </form>

        <form id="join-company-form" method="POST" action="{{ route('companies.join') }}" class="hidden">
            @csrf
            <input type="hidden" name="invitation_code" :value="invitationCode">
        </form>

        <!-- Отображение ошибок -->
        @if($errors->any())
        <div class="mt-6 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Переключение темной темы
    function toggleDarkMode() {
        const container = document.getElementById('app-container');
        const isDark = !document.documentElement.classList.contains('dark');

        // Применяем новый унифицированный подход к теме
        if(isDark) {
            // Включаем темную тему
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
            localStorage.setItem('darkMode', 'true');
        } else {
            // Включаем светлую тему
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            localStorage.setItem('darkMode', 'false');
        }

        // Меняем класс контейнера для обратной совместимости
        if(isDark) {
            container.classList.add('dark-mode');
        } else {
            container.classList.remove('dark-mode');
        }
    }

    // Проверяем сохраненные настройки темы
    document.addEventListener('DOMContentLoaded', function() {
        const darkMode = localStorage.getItem('darkMode') === 'true';
        const container = document.getElementById('app-container');

        if (darkMode) {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
            container.classList.add('dark-mode');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            container.classList.remove('dark-mode');
        }
    });

    function onboardingApp() {
        return {
            mode: 'create', // create или join
            currentStep: 1,
            showCheckAnimation: false,
            company: {
                name: '',
                email: '',
                phone: '',
                description: ''
            },
            invitationCode: '',

            // Переключение режима
            setMode(newMode) {
                // Анимация переключения
                const selector = document.querySelector('.tab-selector');
                if (window.gsap) {
                    gsap.to(selector, {
                        keyframes: {
                            '0%': { scale: 1 },
                            '50%': { scale: 0.98 },
                            '100%': { scale: 1 }
                        },
                        duration: 0.3
                    });
                }

                this.mode = newMode;
                this.currentStep = 1;
            },

            // Классы для анимации шагов
            getStepClass(step) {
                if (step === this.currentStep) return 'active';
                if (step < this.currentStep) return 'before';
                return 'after';
            },

            // Валидация и переход к следующему шагу
            validateStep(step) {
                if (step === 1) {
                    if (!this.company.name || this.company.name.length < 2) {
                        this.shakeElement(document.getElementById('company_name'));
                        return;
                    }

                    // Показываем анимацию проверки
                    this.showCheckAnimation = true;
                    setTimeout(() => {
                        this.showCheckAnimation = false;
                        this.nextStep();
                    }, 1000);
                } else if (step === 2) {
                    if (!this.company.email || !this.validateEmail(this.company.email)) {
                        this.shakeElement(document.getElementById('company_email'));
                        return;
                    }
                    this.nextStep();
                }
            },

            // Проверка email
            validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            },

            // Переход к следующему шагу
            nextStep() {
                if (this.currentStep < 3) {
                    this.currentStep++;
                    this.animateStepTransition();
                }
            },

            // Переход к предыдущему шагу
            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    this.animateStepTransition();
                }
            },

            // Анимация перехода между шагами
            animateStepTransition() {
                const container = document.querySelector('.form-container');
                if (window.gsap) {
                    gsap.fromTo(container,
                        { rotate: -1, scale: 0.98 },
                        { rotate: 0, scale: 1, duration: 0.5, ease: "elastic.out(1, 0.3)" }
                    );
                }
            },

            // Анимация ошибки валидации
            shakeElement(element) {
                if (window.gsap) {
                    gsap.to(element, {
                        keyframes: {
                            '0%': { x: 0 },
                            '25%': { x: -10 },
                            '50%': { x: 10 },
                            '75%': { x: -10 },
                            '100%': { x: 0 }
                        },
                        duration: 0.5,
                        ease: "power2.out"
                    });
                }

                // Добавляем красный цвет
                element.style.borderColor = 'var(--error-color)';
                setTimeout(() => {
                    element.style.borderColor = '';
                }, 2000);
            },

            // Отправка формы создания компании
            submitForm() {
                document.getElementById('create-company-form').submit();
            },

            // Отправка формы присоединения к компании
            joinCompany() {
                if (!this.invitationCode || this.invitationCode.length < 6) {
                    this.shakeElement(document.getElementById('invitation_code'));
                    return;
                }
                document.getElementById('join-company-form').submit();
            }
        }
    }

    // Инициализируем Alpine.js вручную, если он еще не инициализирован
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Alpine === 'undefined') {
            console.warn('Alpine.js не загружен. Загружаем из CDN...');
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js';
            script.defer = true;
            document.head.appendChild(script);

            script.onload = function() {
                window.Alpine = Alpine;
                Alpine.start();
            };
        }

        // Проверяем доступность GSAP
        if (typeof gsap === 'undefined') {
            console.warn('GSAP не загружен. Загружаем из CDN...');
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js';
            document.head.appendChild(script);
        }
    });
</script>
@endpush
