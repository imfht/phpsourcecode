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
use app\system\event\AppConfig;

class Manage extends Base {

    protected $user;                  //用户信息
    protected $member;                //用户信息(兼容处理)
    protected $member_miniapp_id = 0; //应用ID
    protected $member_miniapp;        //应用信息
    
    /**
     * 初始化类
     */
    protected function initialize(){ 
        parent::initialize();
        $this->user   = self::isLogin(); //如果登录返回当前登录的用户信息
        $this->member = $this->user;     //兼容处理
        //当前用户管理的应用
        if(Passport::getMiniapp()){
            $this->member_miniapp_id = Passport::getMiniapp()['member_miniapp_id'];
            $this->member_miniapp    = SystemMemberMiniapp::where(['id' => $this->member_miniapp_id])->find();
            if(!$this->member_miniapp){
                Passport::clearMiniapp();
            }
            $this->isAppTyes($this->member_miniapp->miniapp->types);
        }
        self::isAuth();  //权限判断
        $assign['member_miniapp_id'] = $this->member_miniapp_id;
        $assign['member_miniapp']    = $this->member_miniapp;
        $assign['user']              = $this->user;
        $assign['member']            = $this->member; //兼容处理
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

    /**
     * 登录成功以后判断是否有访问权限
     * @access protected
     * @return boolean
     */
    protected function isAuth(){
        $module     = $this->request->module();
        $controller = strtolower($this->request->controller());
        $action     = $this->request->action();
        //判断是否系统应用\是否登录\是否创始人
        if($module == 'system' || !$this->member){
            return;
        }
        if(!$this->member_miniapp){
            $this->error('你未开通任何应用');
        }
        if($module != $this->member_miniapp->miniapp->miniapp_dir){
            $this->error('禁止跨应用管理,请先开通或切换管理应用。');
        }
        if($this->member->parent_id == 0 || $this->member->auth == 0){
            return;
        }
        $authconfig = AppConfig::auth($this->member_miniapp->miniapp->miniapp_dir);
        if(empty($authconfig)){
            return;
        }
        //权限判断
        foreach ($authconfig as $key => $value) {
            if($this->member->auth == $value['auth'] && isset($value['group'])){
                if(!isset($value['group'][$controller])){
                    $this->error('你无权限访问当前功能,请联系创始人更改你的权限。');
                }
                if(!empty($value['group'][$controller]['action']) && !in_array($action,$value['group'][$controller]['action'])){
                    $this->error('你无权限访问当前功能,请联系创始人更改你的权限。');
                }
                break;
            }
        }
        return;
    }
}