<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Публичные маршруты
Route::get('/', [HomeController::class, 'index'])->name('home');

// Маршруты аутентификации
Route::middleware(['guest'])->group(function () {
    // Авторизация
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Регистрация
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Выход из системы (доступен всем)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Защищенные маршруты (требуют авторизации)
Route::middleware(['auth'])->group(function () {
    // Альтернативный маршрут для выхода через GET
    Route::get('/logout', [LoginController::class, 'logout']);

    // Маршруты для работы с компаниями (доступны всем авторизованным пользователям)
    Route::prefix('companies')->name('companies.')->group(function () {
        // Создание и присоединение к компании
        Route::get('/create', [CompanyController::class, 'create'])->name('create');
        Route::post('/', [CompanyController::class, 'store'])->name('store');
        Route::post('/join', [CompanyController::class, 'join'])->name('join');
    });

    // Маршруты, требующие наличия компании у пользователя
    Route::middleware(['company'])->group(function () {
        // Дашборд
        Route::get('/dashboard', function () {
            return view('home.dashboard');
        })->name('dashboard');

        // Управление приглашениями в компанию
        Route::prefix('companies/invitations')->name('companies.invitations')->group(function () {
            Route::get('/', [CompanyController::class, 'showInvitations'])->name('');
            Route::post('/', [CompanyController::class, 'createInvitation'])->name('.create');
        });

        // Здесь можно добавить другие маршруты, требующие наличия компании
        // например: заявки, аналитика, настройки компании и т.д.
    });
});

Route::get('/preloader-example', function () {
    return view('components.preloader-example');
})->name('preloader.example');
