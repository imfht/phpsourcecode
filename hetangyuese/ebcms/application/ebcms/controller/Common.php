<?php
namespace app\ebcms\controller;
use think\Controller;
class Common extends Controller
{

    public function _initialize()
    {

        \think\Session::boot();
        \think\Session::prefix(\think\Config::get('session.prefix').'admin');

        if (!\think\Session::get('manager_id')) {
            $this->redirect('ebcms/auth/login');
        }

        \think\Hook::listen('ebcms_init');

        if (request()->isPost()) {
            // 开启日志记录
            if (false !== \ebcms\Config::get('system.base.oplog_on')) {
                \think\Hook::add('app_end', 'app\\ebcms\\behavior\\Oplog');
            }
        }
    }
}