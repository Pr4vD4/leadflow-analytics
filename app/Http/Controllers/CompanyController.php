<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Показать форму создания компании
     */
    public function create()
    {
        // Проверяем, есть ли уже компания у пользователя
        if (Auth::user()->hasCompany()) {
            return redirect('/dashboard');
        }

        return view('companies.create');
    }

    /**
     * Сохранить новую компанию
     */
    public function store(Request $request)
    {
        // Проверяем, есть ли уже компания у пользователя
        if (Auth::user()->hasCompany()) {
            return redirect('/dashboard');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Создаем новую компанию
            $company = Company::create([
                'name' => $request->name,
                'email' => Auth::user()->email,
                'phone' => $request->phone,
                'description' => $request->description,
                'is_active' => true,
            ]);

            // Привязываем пользователя к компании
            $user = Auth::user();
            $user->company_id = $company->id;
            $user->save();

            return redirect('/dashboard')->with('success', 'Компания успешно создана!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Произошла ошибка при создании компании. Пожалуйста, попробуйте еще раз.'
            ]);
        }
    }

    /**
     * Присоединиться к существующей компании по коду приглашения
     */
    public function join(Request $request)
    {
        // Проверяем, есть ли уже компания у пользователя
        if (Auth::user()->hasCompany()) {
            return redirect('/dashboard');
        }

        $validator = Validator::make($request->all(), [
            'invitation_code' => ['required', 'string', 'size:8'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Ищем активное приглашение с указанным кодом
        $invitation = CompanyInvitation::where('code', $request->invitation_code)
            ->where('is_active', true)
            ->first();

        if (!$invitation) {
            return back()->withInput()->withErrors([
                'invitation_code' => 'Код приглашения недействителен или уже использован.',
            ]);
        }

        try {
            // Присоединяем пользователя к компании
            $user = Auth::user();
            $user->company_id = $invitation->company_id;
            $user->save();

            // Деактивируем код приглашения
            $invitation->deactivate($user);

            // Назначаем пользователю роль из метаданных приглашения
            if ($invitation->metadata && isset($invitation->metadata['role'])) {
                $user->assignRole($invitation->metadata['role']);
            }

            return redirect()->route('crm.dashboard')->with('success', 'Вы успешно присоединились к компании!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Произошла ошибка при присоединении к компании. Пожалуйста, попробуйте еще раз.'
            ]);
        }
    }

    /**
     * Создать новый код приглашения для компании
     */
    public function createInvitation(Request $request)
    {
        // Проверяем, имеет ли пользователь компанию
        if (!Auth::user()->hasCompany()) {
            return back()->withErrors([
                'error' => 'У вас нет компании для создания приглашения.'
            ]);
        }

        try {
            $company = Auth::user()->company;
            $invitation = $company->createInvitation(Auth::user());

            return back()->with('success', 'Код приглашения успешно создан: ' . $invitation->code);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Произошла ошибка при создании кода приглашения. Пожалуйста, попробуйте еще раз.'
            ]);
        }
    }

    /**
     * Показать список кодов приглашения для компании
     */
    public function showInvitations()
    {
        // Проверяем, имеет ли пользователь компанию
        if (!Auth::user()->hasCompany()) {
            return redirect()->route('companies.create');
        }

        $invitations = Auth::user()->company->invitations()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('companies.invitations', compact('invitations'));
    }
}
