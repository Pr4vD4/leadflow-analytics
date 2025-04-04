@extends('layouts.crm')

@section('title', 'Управление пользователями')

@section('content')
    <div class="container mx-auto">
        <!-- Заголовок страницы -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Настройки компании</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Управление пользователями и доступом</p>
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
                <a href="{{ route('crm.settings.integrations') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600">
                    Интеграции
                </a>
                <a href="{{ route('crm.settings.users') }}" class="whitespace-nowrap pb-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-500">
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

        <!-- Список пользователей -->
        <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Пользователи ({{ count($users) }})</h3>
            </div>
            <div class="overflow-x-auto">
                @if(count($users) > 0)
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-secondary-700">
                        <thead class="bg-gray-50 dark:bg-secondary-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Пользователь
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Роль
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Статус
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Дата регистрации
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Действия
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-secondary-800 divide-y divide-gray-200 dark:divide-secondary-700">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ $user->name }}">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $user->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->roles->count() > 0)
                                            @php
                                                $role = $user->roles->first();
                                                $roleClasses = [
                                                    'admin' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                    'manager' => 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300',
                                                    'employee' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                                ];
                                                $roleClass = $roleClasses[$role->name] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleClass }}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                                Без роли
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Активен
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $user->created_at->format('d.m.Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            @if(Auth::id() != $user->id && Auth::user()->hasPermissionTo('edit-users'))
                                                <button type="button" class="text-primary-600 dark:text-primary-500 hover:text-primary-900 dark:hover:text-primary-400" onclick="openEditModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->roles->first()->name ?? '' }}')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                            @if(Auth::id() != $user->id && Auth::user()->hasPermissionTo('delete-users'))
                                                <button type="button" class="text-red-600 dark:text-red-500 hover:text-red-900 dark:hover:text-red-400">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-10">
                        <i class="fas fa-users text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">В компании нет пользователей</p>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Пригласите сотрудников для совместной работы</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Приглашения -->
        <div class="bg-white dark:bg-secondary-800 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-secondary-700 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Приглашения</h3>
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800" onclick="openModal('create-invitation-modal')">
                    <i class="fas fa-link mr-2"></i>
                    Создать одноразовое приглашение
                </button>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Отправьте приглашение новому сотруднику для работы в LeadFlow Analytics
                </p>

                <form action="{{ route('crm.settings.invite-user') }}" method="POST" class="mb-6 space-y-4">
                    @csrf
                    <div>
                        <label for="invite_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <div class="flex">
                            <input type="email" id="invite_email" name="email" class="flex-grow focus:ring-primary-500 focus:border-primary-500 block shadow-sm sm:text-sm border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white rounded-l-md" placeholder="email@example.com" required>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-r-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Отправить
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="invite_role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Роль</label>
                        <select id="invite_role" name="role" class="block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Администраторы имеют полный доступ ко всем настройкам и данным. Менеджеры имеют доступ к управлению пользователями и заявками. Сотрудники могут только создавать и редактировать заявки.
                        </p>
                    </div>
                </form>

                <div class="border-t border-gray-200 dark:border-secondary-700 pt-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Активные приглашения</h4>

                    @if(isset($invitations) && count($invitations) > 0)
                        <div class="bg-gray-50 dark:bg-secondary-700 rounded-md overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-secondary-600">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-secondary-600">
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Код приглашения
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Создан
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Статус
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Действия
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-secondary-600">
                                    @foreach($invitations as $invitation)
                                        <tr class="bg-white dark:bg-secondary-800">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $invitation->code }}
                                                <button type="button" onclick="copyToClipboard('{{ $invitation->code }}')" class="ml-2 text-primary-600 dark:text-primary-500 hover:text-primary-800 dark:hover:text-primary-400" title="Копировать код">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $invitation->created_at->format('d.m.Y H:i') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                @if($invitation->is_active)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                        Активно
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        Использовано
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                @if($invitation->is_active)
                                                    <button type="button" class="text-red-600 dark:text-red-500 hover:text-red-900 dark:hover:text-red-400"
                                                        onclick="if(confirm('Вы уверены, что хотите деактивировать это приглашение?')) { document.getElementById('deactivate-invitation-{{ $invitation->id }}').submit(); }">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <form id="deactivate-invitation-{{ $invitation->id }}" action="{{ route('crm.settings.deactivate-invitation', $invitation->id) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('PUT')
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-gray-50 dark:bg-secondary-700 p-4 rounded-md text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                На данный момент нет активных приглашений
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Модальное окно редактирования роли пользователя -->
        <div id="edit-role-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden" x-cloak>
            <div class="relative bg-white dark:bg-secondary-800 rounded-lg max-w-md w-full mx-4 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Изменение роли пользователя</h3>
                    <button onclick="closeModal('edit-role-modal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="edit-role-form" action="" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Пользователь</label>
                        <p id="edit-user-name" class="text-sm text-gray-900 dark:text-white"></p>
                    </div>

                    <div class="mb-4">
                        <label for="edit_role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Роль</label>
                        <select id="edit_role" name="role" class="block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('edit-role-modal')" class="px-4 py-2 border border-gray-300 dark:border-secondary-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-secondary-700 hover:bg-gray-50 dark:hover:bg-secondary-600 focus:outline-none">
                            Отмена
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                            Сохранить
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Модальное окно создания одноразового приглашения -->
        <div id="create-invitation-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden" x-cloak>
            <div class="relative bg-white dark:bg-secondary-800 rounded-lg max-w-md w-full mx-4 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Создание одноразового приглашения</h3>
                    <button onclick="closeModal('create-invitation-modal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('crm.settings.create-invitation') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Вы можете создать одноразовое приглашение для присоединения к компании.
                            Этот код можно будет передать новому сотруднику любым удобным способом.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label for="invitation_role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Роль</label>
                        <select id="invitation_role" name="role" class="block w-full rounded-md border-gray-300 dark:border-secondary-700 dark:bg-secondary-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Пользователь, который воспользуется этим приглашением, получит указанную роль.
                        </p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('create-invitation-modal')" class="px-4 py-2 border border-gray-300 dark:border-secondary-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-secondary-700 hover:bg-gray-50 dark:hover:bg-secondary-600 focus:outline-none">
                            Отмена
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-secondary-800">
                            Создать приглашение
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @push('scripts')
        <script>
            function openModal(id) {
                document.getElementById(id).classList.remove('hidden');
            }

            function closeModal(id) {
                document.getElementById(id).classList.add('hidden');
            }

            function openEditModal(userId, userName, userRole) {
                document.getElementById('edit-user-name').textContent = userName;
                document.getElementById('edit-role-form').action = '{{ route("crm.settings.update-user-role", "") }}/' + userId;

                // Установить текущую роль пользователя в выпадающем списке
                if (userRole) {
                    const roleSelect = document.getElementById('edit_role');
                    for (let i = 0; i < roleSelect.options.length; i++) {
                        if (roleSelect.options[i].value === userRole) {
                            roleSelect.selectedIndex = i;
                            break;
                        }
                    }
                }

                openModal('edit-role-modal');
            }

            function copyToClipboard(text) {
                // Создаем временный textarea для копирования
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed'; // Избегаем прокрутки
                document.body.appendChild(textarea);
                textarea.select();

                try {
                    // Копируем текст
                    document.execCommand('copy');

                    // Показываем уведомление
                    const notice = document.createElement('div');
                    notice.textContent = 'Код скопирован!';
                    notice.style.position = 'fixed';
                    notice.style.bottom = '20px';
                    notice.style.left = '50%';
                    notice.style.transform = 'translateX(-50%)';
                    notice.style.padding = '10px 16px';
                    notice.style.backgroundColor = 'rgba(16, 185, 129, 0.9)';
                    notice.style.color = 'white';
                    notice.style.borderRadius = '6px';
                    notice.style.zIndex = '9999';
                    notice.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
                    document.body.appendChild(notice);

                    // Удаляем уведомление через 2 секунды
                    setTimeout(() => {
                        document.body.removeChild(notice);
                    }, 2000);
                } catch (err) {
                    console.error('Не удалось скопировать текст: ', err);
                }

                // Удаляем временный textarea
                document.body.removeChild(textarea);
            }
        </script>
        @endpush
    </div>
@endsection
