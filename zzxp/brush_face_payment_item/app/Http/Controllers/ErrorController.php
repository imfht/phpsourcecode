<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class ErrorController extends Controller
{
	public function index()
	{
		return \View::make('errors.msg')->with('msg','没有权限');
	}
}