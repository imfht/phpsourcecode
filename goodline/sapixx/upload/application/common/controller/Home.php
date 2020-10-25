<?php
/**
 * @copyright   Copyright (c) 2018 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 浏览器无权限访问控制基类
 */
namespace app\common\controller;
use think\facade\Request;
use app\common\event\User;


class Home extends Base{

    protected $member_miniapp    = []; //应用信息
    protected $member_miniapp_id = 0;  //应用信息ID
    protected $member            = []; //应用创始人信息
    protected $user;                   //登录后用户

    /**
     * 初始化类
     */
    protected function initialize(){
        parent::initialize();
        $this->member_miniapp  = self::apiAccess(); 
        if(!$this->member_miniapp){
            $this->error('应用停止服务');
        }
        $this->user                  = User::getUser(); 
        $this->member_miniapp_id     = $this->member_miniapp->id;
        $this->member                = $this->member_miniapp->member;  //应用创始人信息
        $assign['member_miniapp']    = $this->member_miniapp;
        $assign['member_miniapp_id'] = $this->member_miniapp_id;
        $assign['member']            = $this->member;
        $assign['user']              = $this->user;
        $this->isAppTyes($this->member_miniapp->miniapp->types);
        $this->assign($assign);
    }

    /**
     * 禁止用户登录
     */
    protected function isUserAuth($url = ''){
        if(!$this->user){
            $this->error('用户认证失败',$url);
        }
    }
}