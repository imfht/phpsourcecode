<?php
namespace app\admin\controller;

use think\Controller;
use utils\JWTUtils;

class Base extends Controller
{
    public function initialize()
    {
        if(!session('auth')||!session('auth_sign')){
            $this->redirect('admin/login/index');
        }

        // 控制器初始化
        $this->_we();

//        $this->error('抱歉，您没有操作权限');
        $auths = new \auth\Auth();
        $module     = strtolower(request()->module());
        $controller = strtolower(request()->controller());
        $action     = strtolower(request()->action());
        $url        = "/".$module."/".$controller."/".$action;

//        if(session('auth_abilities')!=1){
        $auth = JWTUtils::decode(session('auth'));
        if($auth->id != 1){
//            if(session('auth')['id'] != 1){
            if(!in_array($url, ['/admin/index/index','/admin/index/dashboard'])){
                $auth_abilities = JWTUtils::decode(session('auth_abilities'));
//                if(!$auth->check($url,session('auth_abilities'))){
                if(!$auths->check($url, $auth_abilities)){
                    $this->error('暂无权限');
                }
            }
        }
//        }
    }

    // 初始化
    protected function _we()
    {
    }
}