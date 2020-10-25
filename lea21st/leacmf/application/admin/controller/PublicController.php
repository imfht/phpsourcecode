<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/12/29
 * Time: 16:32
 */

namespace app\admin\controller;

use app\admin\model\Admin;
use app\common\library\Hash;
use think\facade\Request;
use think\Validate;

class PublicController extends BaseController
{

    public function login()
    {
        if ($this->request->isPost()) {
            $post     = $this->request->only(['username', 'password', 'captcha'], 'post');
            $validate = Validate::make([
                'username|用户名' => 'require|max:32',
                'password|密码'  => 'require|length:6,16',
                'captcha|验证码'  => 'require|captcha',
            ]);
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }

            $admin = Admin::get(['username' => $post['username']]);
            if (!$admin) {
                $this->error('用户名不存在');
            }
            if (!Hash::check($post['password'], $admin['password'])) {
                $this->error('密码错误');
            }
            if (1 != $admin['status']) {
                $this->error('该用户已被禁用，无法登陆');
            }

            //登录成功
            $admin['login_times']     = $admin['login_times'] + 1;
            $admin['last_login_ip']   = Request::ip();
            $admin['last_login_time'] = time();
            $admin['token']           = md5($admin['username'] . $admin['password'] . uniqid());
            if ($admin->save() && app()->rbac->login($admin)) {
                cookie('username', $admin['username']);
                $this->success('登录成功', 'index/index');
            }
            $this->error('登录失败');
        } else {
            if (!empty(app()->user)) {
                return redirect(url('/'));
            }
            return view();
        }
    }

    public function logout()
    {
        app()->rbac->logout();
        return redirect('login');
    }
}