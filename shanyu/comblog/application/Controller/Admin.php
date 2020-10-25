<?php
namespace App\Controller;

use Kernel\Config;
use Kernel\Loader;

use App\Service\Account;

class Admin extends Controller
{
    public function _initialize()
    {

    }
    public function dispatch()
    {
        Config::instance()->set('view_theme','admin');

        if(isset($_GET['c'])){
            $controller = htmlspecialchars($_GET['c']);
        }else{
            $controller = 'Account';
        }
        if(isset($_GET['a'])){
            $action = htmlspecialchars($_GET['a']);
        }else{
            $action = 'index';
        }

        //判断登录
        if( !in_array($controller, ['Account']) ){
            $uid = Loader::singleton(Account::class)->isLogin();
            if(!$uid) return $this->error('请先登录后台','/admin?c=Account&a=index');
            define('UID',$uid);
        }

        if(!is_file(APP_PATH.'Controller/Admin/'.$controller.'.php')){
            show_404();
        }

        try{
            $controller_name = '\\App\\Controller\\Admin\\'.$controller;
            $controller_class = new $controller_name();
        }catch(\Exception $e){
            show_404();
        }

        if(!method_exists($controller_class, $action)){
            show_404();
        }

        $result = call_user_func([$controller_class,$action]);
        return $result;
    }
}