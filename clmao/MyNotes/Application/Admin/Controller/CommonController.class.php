<?php

namespace Admin\Controller;

use Think\Controller;

class CommonController extends Controller {
    
    /*
     * 权限验证 RBAC验证
     */
    public function _initialize() {
        $checkLogin = session('admin');
        if (empty($checkLogin)) {
            $this->redirect('/Home/Index/login', 0);
        }
        $auth_key = session(C('USER_AUTH_KEY'));
        if (empty($auth_key)) {
           $this->redirect('/Home/Index/login');
        }
        $noAuth = in_array(MODULE_NAME,  explode(',', C('NOT_AUTH_MOUDULE'))) || 
                in_array(ACTION_NAME,  explode(',', C('NOT_AUTH_ACTION')));
        if (C('USER_AUTH_ON') && !$noAuth) {
            $rbac = new \Org\Util\Rbac();
            //p($_SESSION);
            $rbac::AccessDecision() || $this->error('抱歉，你当前用户权限不足',U('/Admin/Admin/help'));
           // die;
        }
        $this->app_path = __APP__;
    }

}
