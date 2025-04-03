<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Показать главную страницу сайта
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('home.index');
    }
}
