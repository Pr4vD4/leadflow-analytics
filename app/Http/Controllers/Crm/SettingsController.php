<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\CompanyInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class SettingsController extends Controller
{
    /**
     * Конструктор контроллера настроек
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage-users')->only(['users', 'updateUserRole', 'inviteUser', 'createInvitation', 'deactivateInvitation']);
    }

    /**
     * Отображает страницу общих настроек компании
     *
     * @return \Illuminate\View\View
     */
    public function general()
    {
        $company = Auth::user()->company;
        return view('crm.settings.general', compact('company'));
    }

    /**
     * Обновляет общие настройки компании
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $company = Auth::user()->company;
        $company->update($request->only(['name', 'phone', 'email', 'address']));

        return redirect()->route('crm.settings.general')->with('success', 'Настройки компании успешно обновлены');
    }

    /**
     * Отображает страницу настройки API
     *
     * @return \Illuminate\View\View
     */
    public function api()
    {
        $company = Auth::user()->company;
        return view('crm.settings.api', compact('company'));
    }

    /**
     * Генерирует новый API ключ для компании
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regenerateApiKey()
    {
        $company = Auth::user()->company;
        $company->api_key = Str::random(32);
        $company->save();

        return redirect()->route('crm.settings.api')->with('success', 'API ключ успешно обновлен');
    }

    /**
     * Отображает страницу интеграций
     *
     * @return \Illuminate\View\View
     */
    public function integrations()
    {
        $company = Auth::user()->company;
        return view('crm.settings.integrations', compact('company'));
    }

    /**
     * Обновляет настройки интеграций
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateIntegrations(Request $request)
    {
        $request->validate([
            'bitrix24_webhook_url' => 'nullable|url|max:255',
            'telegram_chat_id' => 'nullable|string|max:100',
        ]);

        $company = Auth::user()->company;

        // Сохраняем настройки интеграций в JSON-поле settings
        $settings = $company->settings ?? [];
        $settings['integrations'] = [
            'bitrix24' => [
                'webhook_url' => $request->bitrix24_webhook_url,
                'enabled' => (bool) $request->bitrix24_enabled,
            ],
            'telegram' => [
                'chat_id' => $request->telegram_chat_id,
                'enabled' => (bool) $request->telegram_enabled,
            ],
        ];

        $company->settings = $settings;
        $company->save();

        return redirect()->route('crm.settings.integrations')->with('success', 'Настройки интеграций успешно обновлены');
    }

    /**
     * Отображает страницу пользователей
     *
     * @return \Illuminate\View\View
     */
    public function users()
    {
        $company = Auth::user()->company;
        $users = User::where('company_id', $company->id)->get();
        $roles = Role::where('name', '!=', 'admin')->get(); // Исключаем роль админа для обычных пользователей, если текущий пользователь не админ
        $invitations = CompanyInvitation::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if (Auth::user()->hasRole('admin')) {
            $roles = Role::all(); // Администраторы могут видеть все роли
        }

        return view('crm.settings.users', compact('company', 'users', 'roles', 'invitations'));
    }

    /**
     * Обновляет роль пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUserRole(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        // Проверяем, что пользователь относится к нашей компании
        $company = Auth::user()->company;
        $user = User::where('company_id', $company->id)->findOrFail($userId);

        // Запрещаем менять роль самому себе для избежания потери прав
        if ($user->id === Auth::id()) {
            return redirect()->route('crm.settings.users')
                ->with('error', 'Вы не можете изменить свою собственную роль');
        }

        // Удаляем текущие роли и назначаем новую
        $user->syncRoles([$request->role]);

        return redirect()->route('crm.settings.users')
            ->with('success', 'Роль пользователя успешно обновлена');
    }

    /**
     * Отправляет приглашение новому пользователю
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inviteUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|exists:roles,name',
        ]);

        // Здесь будет логика приглашения пользователя
        // В MVP можно просто создать пользователя с временным паролем или отправить email с инвайтом

        return redirect()->route('crm.settings.users')
            ->with('success', 'Приглашение отправлено на указанный email');
    }

    /**
     * Создает одноразовое приглашение для присоединения к компании
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createInvitation(Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $company = Auth::user()->company;
        $code = CompanyInvitation::generateUniqueCode();

        $invitation = new CompanyInvitation();
        $invitation->code = $code;
        $invitation->company_id = $company->id;
        $invitation->user_id = Auth::id();
        $invitation->is_active = true;
        $invitation->save();

        // Сохраняем выбранную роль в метаданных приглашения
        $invitation->metadata = ['role' => $request->role];
        $invitation->save();

        return redirect()->route('crm.settings.users')
            ->with('success', "Приглашение с кодом {$code} успешно создано");
    }

    /**
     * Деактивирует приглашение
     *
     * @param  int  $invitationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivateInvitation($invitationId)
    {
        $company = Auth::user()->company;
        $invitation = CompanyInvitation::where('company_id', $company->id)
            ->where('id', $invitationId)
            ->where('is_active', true)
            ->firstOrFail();

        $invitation->deactivate(Auth::user());

        return redirect()->route('crm.settings.users')
            ->with('success', 'Приглашение успешно деактивировано');
    }
}
