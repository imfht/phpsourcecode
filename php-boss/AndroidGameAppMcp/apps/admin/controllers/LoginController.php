<?php


use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Config;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class LoginController extends Controller
{
	public function getIndex()
	{
		$data = array();
		return View::make('login',$data);
	}
}