<?php namespace App\Http\Controllers;

use Theme;

class WelcomeController extends Controller
{
  public function index()
  {
    return Theme::view('welcome.index',[]);
  }
}
