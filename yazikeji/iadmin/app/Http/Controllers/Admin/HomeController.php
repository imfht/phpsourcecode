<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Facades\Agent;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        return view('admin.home.home');
    }
}
