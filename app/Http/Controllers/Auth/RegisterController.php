<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Показать форму регистрации
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Обработать регистрацию нового пользователя
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            // Создаем новую компанию
            $company = Company::create([
                'name' => $request->company_name,
                'email' => $request->email,
                'is_active' => true,
            ]);

            // Создаем пользователя и привязываем к компании
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_id' => $company->id,
                'is_admin' => false,
            ]);

            // Назначаем роль менеджера
            $user->assignRole('manager');

            DB::commit();

            // Авторизуем пользователя
            Auth::login($user);

            return redirect('/dashboard');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors([
                'error' => 'Произошла ошибка при регистрации. Пожалуйста, попробуйте еще раз.'
            ]);
        }
    }
}
