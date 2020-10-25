<?php
/**
 * Created by PhpStorm.
 * User: carl
 * Date: 2017/3/30
 * Time: 上午9:12
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => 'logout']);
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function guard()
    {
        return auth()->guard('admin');
    }

    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            array_merge($this->credentials($request), ['active'=>1]), $request->has('remember')
        );
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->forget($this->guard()->getName());

        $request->session()->regenerate();

        //return redirect('/');
        return response()->json(['status'=>1]);
    }

}