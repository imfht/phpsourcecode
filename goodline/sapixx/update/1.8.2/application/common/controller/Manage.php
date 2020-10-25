<?php
/**
 * @copyright   Copyright (c) 2018 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商户登录基础控制器
 */
namespace app\common\controller;
use app\common\model\SystemMemberMiniapp;
use app\common\event\Passport;

class Manage extends Base {

    protected $user;                  //用户信息
    protected $member_miniapp_id = 0; //当前管理用户小程序ID
    protected $member_miniapp;        //当前管理的小程序信息
    
    /**
     * 初始化类
     */
    protected function initialize(){ 
        parent::initialize();
        $this->user = self::isLogin();      //如果登录返回当前登录的用户信息
        if(Passport::getMiniapp()){
            $this->member_miniapp_id = Passport::getMiniapp()['member_miniapp_id'];
            $this->member_miniapp    = SystemMemberMiniapp::where(['id' => $this->member_miniapp_id])->find();
            if(!$this->member_miniapp){
                Passport::clearMiniapp();
            }
        }
        $assign['member_miniapp_id'] = $this->member_miniapp_id;
        $assign['member_miniapp']    = $this->member_miniapp;
        $assign['user']              = $this->user;
        $this->assign($assign);
    }
    
    
    /**
     * 判断管理员是否登录
     * @access protected
     * @return boolean
     */
    protected function isLogin(){
        //不需要登录验证的页面
        $noLogin = ['system' =>['Passport.login'=>['index','reg','getpassword','logout','getregsms','getloginsms','cloud']]];
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
        $getUser = Passport::getUser();
        return $getUser ? $getUser : $this->redirect('system/passport.login/logout',302);
    }
}
