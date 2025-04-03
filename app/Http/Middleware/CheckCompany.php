<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Если пользователь не имеет компании, перенаправляем на страницу создания компании
        if (Auth::check() && !Auth::user()->hasCompany()) {
            // Исключаем маршруты, связанные с созданием компании
            if (!$request->routeIs('companies.create') && !$request->routeIs('companies.store')) {
                return redirect()->route('companies.create');
            }
        }

        return $next($request);
    }
}
