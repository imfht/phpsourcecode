<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Theme;

class IndexController extends Controller
{
    public function index()
    {
      return Theme::view('user.index');
    }
}
