<?php

namespace Apphook;

use SCH60\Kernel\App;
use Common\AppCustomHelper;
use SCH60\Kernel\KernelHelper;
use SCH60\Kernel\StrHelper;

class InitController{
    
    protected $allowNoLogin;
    
    public function __construct(){
        $this->allowNoLogin = KernelHelper::config('controller_allow_no_login_action');
    }
    
    
    public function run(){
        $hookCommon = KernelHelper::getInstance("Apphook\Common");
        $hookCommon->disableSearchRobot();
        $hookCommon->check_post_referer();
        $this->run_isAllowLogin();
        $hookCommon->initAlibabaSDK();
    }
    
    
    public function run_isAllowLogin(){
    
        if($this->isAllowNoLogin()){
            return ;
        }
    
        if(!AppCustomHelper::isLogin()){
            App::$app->response->error("尚未登录", 401, StrHelper::url('user/login/index'));
        }
        
    }
    
    protected function isAllowNoLogin(){
    
        $router = App::$app->getRouter();
    
        $routerStr = strtolower($router['router']);
    
        if("*" === $this->allowNoLogin){
            return true;
        }
    
        if(empty($this->allowNoLogin)){
            return false;
        }
    
        if(in_array($routerStr, $this->allowNoLogin)){
            return true;
        }
    
        return false;
    
    }
    
    
}