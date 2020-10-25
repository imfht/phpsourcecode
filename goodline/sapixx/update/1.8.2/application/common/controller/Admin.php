<?php
/**
 * @copyright   Copyright (c) 2018 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 后台管理基础控制器
 */
namespace app\common\controller;
use app\common\model\SystemMiniapp;
use app\common\event\Admin as AdminLogin;
class Admin extends Base {

    protected $miniapp = [];  //当前管理应用

    protected function initialize(){
        parent::initialize();
        self::isLogin(); //判断是否登录
        $miniapp_id = AdminLogin::getMiniapp();
        if($miniapp_id){
            $this->miniapp = SystemMiniapp::where(['id' => $miniapp_id])->field('id,title,is_openapp,miniapp_dir')->find();
        }
        $this->assign(['miniapp'=> $this->miniapp]);
    }

    /**
     * 判断管理员是否登录
     * @access protected
     * @return boolean
     */
    protected function isLogin(){
        //不需要登录验证的页面
        $noLogin = ['system' =>['Admin.index'=>['login','logout']]];
        //当前请求方法
        $module     = $this->request->module();
        $controller = $this->request->controller();
        $action     = $this->request->action();
        //如果当前访问是无需登录验证则直接返回  
        if(isset($noLogin[$module])){
            if(isset($noLogin[$module][$controller]) && in_array($action,$noLogin[$module][$controller])){
                return true;
            }
        }
        if(!AdminLogin::getLoginSession()){
            AdminLogin::setlogoutSession(); 
            return $this->redirect('system/admin.index/logout',302);
        }
        return true;
    }
}
