<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Конструктор контроллера дашборда
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Отображает страницу дашборда
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // В будущем здесь будет логика получения данных для дашборда
        return view('crm.dashboard.index');
    }
}
