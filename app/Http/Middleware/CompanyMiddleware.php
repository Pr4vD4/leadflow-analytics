<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CompanyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверка авторизации
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Если пользователь админ, он имеет доступ ко всем компаниям
        if ($user->is_admin) {
            return $next($request);
        }

        // Проверка наличия company_id в запросе и доступа пользователя к этой компании
        $companyId = $request->route('company') ?? $request->input('company_id');

        // Если запрашивается конкретная компания
        if ($companyId) {
            // Пользователь может иметь доступ только к своей компании
            if ($user->company_id != $companyId) {
                abort(403, 'У вас нет доступа к данной компании');
            }
        } else {
            // Если company_id не указан, устанавливаем в запросе компанию пользователя
            if ($user->company_id) {
                $request->merge(['company_id' => $user->company_id]);
            } else {
                abort(403, 'У вас нет доступа к компаниям');
            }
        }

        return $next($request);
    }
}
