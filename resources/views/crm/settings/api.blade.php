@extends('layouts.crm')

@section('title', 'Настройки API')

@section('content')
    <div class="container mx-auto">
        <!-- Заголовок страницы -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Настройки компании</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Управление API ключами и интеграцией</p>
        </div>

        <!-- Навигация по настройкам -->
        <div class="mb-6 border-b border-gray-200 dark:border-secondary-700">
            <nav class="flex -mb-px space-x-8">
                <a href="{{ route('crm.settings.general') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                    Общие настройки
                </a>
                <a href="{{ route('crm.settings.api') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-500">
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

        <!-- API ключ -->
        <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">API ключ</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    API ключ используется для безопасной интеграции вашего сайта с нашим сервисом. Сохраните его в надежном месте и не передавайте третьим лицам.
                </p>

                <div class="mb-6">
                    <label for="api_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ваш API ключ</label>
                    <div class="flex">
                        <input type="text" id="api_key" value="{{ $company->api_key }}" class="flex-grow focus:ring-primary-500 focus:border-primary-500 block shadow-sm sm:text-sm border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white rounded-l-md" readonly>
                        <button
                            type="button"
                            onclick="copyApiKey()"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-r-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-secondary-800">
                            <i class="fas fa-copy mr-2"></i>
                            Копировать
                        </button>
                    </div>
                </div>

                <form action="{{ route('crm.settings.regenerate-api-key') }}" method="POST" class="mt-4 flex items-center">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-secondary-800">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Сгенерировать новый ключ
                    </button>
                    <span class="ml-3 text-sm text-gray-500 dark:text-gray-400">Внимание! После генерации прежний ключ перестанет работать.</span>
                </form>
            </div>
        </div>

        <!-- Инструкция по интеграции -->
        <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Инструкция по интеграции</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Чтобы начать отправлять заявки в LeadFlow Analytics, вам нужно добавить код формы на ваш сайт. Вот пример интеграции:
                </p>

                <div class="bg-gray-50 dark:bg-secondary-700 rounded-md p-4 mb-4">
                    <pre class="text-xs text-gray-900 dark:text-white overflow-x-auto"><code>&lt;form id="lead-form"&gt;
  &lt;input type="text" name="name" placeholder="Имя"&gt;
  &lt;input type="email" name="email" placeholder="Email"&gt;
  &lt;input type="tel" name="phone" placeholder="Телефон"&gt;
  &lt;textarea name="message" placeholder="Сообщение"&gt;&lt;/textarea&gt;
  &lt;button type="submit"&gt;Отправить&lt;/button&gt;
&lt;/form&gt;

&lt;script&gt;
  document.getElementById('lead-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
      name: formData.get('name'),
      email: formData.get('email'),
      phone: formData.get('phone'),
      message: formData.get('message'),
      source: 'website'
    };

    fetch('{{ config('app.url') }}/api/leads', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-API-Key': '{{ $company->api_key }}'
      },
      body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
      alert('Спасибо! Ваша заявка отправлена.');
      this.reset();
    })
    .catch(error => {
      console.error('Ошибка:', error);
    });
  });
&lt;/script&gt;</code></pre>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Вы также можете использовать наш API для отправки заявок из любых других источников. Подробная документация API доступна по ссылке:
                </p>

                <div class="flex items-center">
                    <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                        <i class="fas fa-book mr-2"></i>
                        Документация API
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyApiKey() {
        const apiKeyElement = document.getElementById('api_key');
        apiKeyElement.select();
        document.execCommand('copy');

        // Показать уведомление об успешном копировании
        alert('API ключ скопирован в буфер обмена');
    }
</script>
@endpush
