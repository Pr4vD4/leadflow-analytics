<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Crm\DashboardController;
use App\Http\Controllers\Crm\LeadController;
use App\Http\Controllers\Crm\SettingsController;

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

        // Управление приглашениями в компанию
        Route::prefix('companies/invitations')->name('companies.invitations')->group(function () {
            Route::get('/', [CompanyController::class, 'showInvitations'])->name('');
            Route::post('/', [CompanyController::class, 'createInvitation'])->name('.create');
        });

        // CRM система
        Route::prefix('crm')->name('crm.')->group(function () {
            // Дашборд CRM
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/dashboard/export-csv', [DashboardController::class, 'exportCsv'])->name('dashboard.export-csv');

            // Заявки
            Route::prefix('leads')->name('leads.')->group(function () {
                Route::get('/', [LeadController::class, 'index'])->name('index');
                Route::get('/export-csv', [LeadController::class, 'exportCsv'])->name('export-csv');
                Route::get('/{id}', [LeadController::class, 'show'])->name('show');
                Route::post('/{id}/update-status', [LeadController::class, 'updateStatus'])->name('update-status');
            });

            // Настройки компании
            Route::prefix('settings')->name('settings.')->group(function () {
                Route::get('/general', [SettingsController::class, 'general'])->name('general');
                Route::post('/general', [SettingsController::class, 'updateGeneral'])->name('update-general');

                Route::get('/api', [SettingsController::class, 'api'])->name('api');
                Route::post('/api/regenerate', [SettingsController::class, 'regenerateApiKey'])->name('regenerate-api-key');

                Route::get('/integrations', [SettingsController::class, 'integrations'])->name('integrations');
                Route::post('/integrations', [SettingsController::class, 'updateIntegrations'])->name('update-integrations');

                Route::get('/users', [SettingsController::class, 'users'])->name('users');
                Route::post('/users/{user}/role', [SettingsController::class, 'updateUserRole'])->name('update-user-role');
                Route::post('/users/invite', [SettingsController::class, 'inviteUser'])->name('invite-user');
                Route::post('/invitations/create', [SettingsController::class, 'createInvitation'])->name('create-invitation');
                Route::put('/invitations/{invitation}/deactivate', [SettingsController::class, 'deactivateInvitation'])->name('deactivate-invitation');
            });

            // Здесь будут другие маршруты CRM (аналитика и т.д.)
        });

        // Здесь можно добавить другие маршруты, требующие наличия компании
        // например: заявки, аналитика, настройки компании и т.д.
    });
});

Route::get('/preloader-example', function () {
    return view('components.preloader-example');
})->name('preloader.example');
