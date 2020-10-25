<?php

namespace App\Http\Controllers;


class IndexController extends WebController
{

    public function __construct()
    {
    }

    public function index()
    {
        return view('welcome');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function docs()
    {
        return view('docs');
    }

    public function changeLocale($locale)
    {
        if (in_array($locale, ['en', 'zh-cn'])) {
            session()->put('locale', $locale);
        }
        return redirect()->back()->withInput();
    }
}
