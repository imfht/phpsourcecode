<?php
namespace app\system\controller;
/*
*
* Created by PhpStorm.
* Author: 初心 [jialin507@foxmail.com]
* Date: 2017/3/15
*/

use think\Cache;
use think\Config;
use think\Controller;
use think\Session;

class Index extends Controller
{
    public function index()
    {
        //登录检查
        if(!Session::has('system_user')){
            $this->redirect('system/login/index');
        }

        return $this->view->fetch();
    }


    public function main() {
        //动态设置配置
        $config = Cache::get('cache_config');
        if(!$config){
            $config = config_lists();
            Cache::set('cache_config',$config);
        }
        Config::set($config);

        return $this->view->fetch();
    }



}
