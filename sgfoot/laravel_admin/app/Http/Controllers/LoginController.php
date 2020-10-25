<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    private $config = [
        'user_id'  => 1000,
        'username' => 'sgfoot.com',
        'password' => 'sgfoot.com'
    ];

    public function login()
    {
        mylog($_SERVER, 'server');
        if ($this->isPost()) {
            $username = $this->request->input('username');
            $password = $this->request->input('password');
            $captcha  = $this->request->input('captcha');
            if (empty($username)) {
                return $this->setJson(10, '请输入用户名称');
            }
            if (empty($password)) {
                return $this->setJson(10, '请输入用户密码');
            }
            if (empty($captcha)) {
                return $this->setJson(10, '请输入验证码');
            }
            if ($username != $this->config['username']) {
                return $this->setJson(10, '用户名不正确');
            }
            if ($password != $this->config['password']) {
                return $this->setJson(10, '密码不正确');
            }
            if (!captcha_check($captcha)) {
                return $this->setJson(20, '验证输入错误');
            }
            session(['user_id' => $this->config['user_id']]);
            return $this->setJson(0, 'ok', route('home'));
        }
        return view('main.login');
    }

    /**
     * 退出
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }

}
