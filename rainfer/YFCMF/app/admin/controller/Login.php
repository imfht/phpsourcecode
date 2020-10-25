<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use app\common\controller\Common;

/**
 * 登录控制器
 * @author rainfer <rainfer520@qq.com>
 */
class Login extends Common
{
    protected function initialize()
    {
        parent::initialize();
    }

    /**
     * 首页
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 登录验证
     */
    public function login()
    {
        if (!request()->isAjax()) {
            $this->error("提交方式错误！", $this->adminpath . '/Login/index');
        } else {
            $this->verifyCheck();
            $username   = input('username');
            $password   = input('password');
            $rememberme = input('rememberme');
            $admin      = new AdminModel;
            if ($admin->login($username, $password, $rememberme)) {
                $this->success('恭喜您，登陆成功', $this->adminpath . '/Index/index');
            } else {
                $this->error($admin->getError(), $this->adminpath . '/Login/index');
            }
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session('admin_auth', null);
        session('admin_auth_sign', null);
        cookie('aid', null);
        cookie('signin_token', null);
        $this->redirect($this->adminpath . '/Login/index');
    }
}
