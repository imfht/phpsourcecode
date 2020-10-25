<?php
/**
 * @copyright   Copyright (c) 2018 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 公众号开发默认继承基类
 */
namespace app\common\controller;
use think\facade\Request;
use app\common\event\User;
use app\common\model\SystemMemberMiniapp;
use encrypter\Encrypter;

class Official extends Base{

    protected $miniapp           = []; //应用信息   
    protected $member_miniapp    = []; //应用信息(兼容处理)
    protected $member_miniapp_id = 0;  //应用信息ID
    protected $service_id;             //应用信息服务ID
    protected $user;                   //登录后用户
    
    /**
     * 初始化类
     */
    protected function initialize(){
        parent::initialize();
        //读取应用
        $this->miniapp  = self::apiAccess();
        if(!$this->miniapp){
            $this->error('应用停止服务');
        }
        //判断是否授权登录
        $this->user = User::getUser();
        if(!$this->user){
            $this->redirect('system/event.wechatMp/putWechat',['app' => $this->miniapp->id,'url' => Encrypter::cpEncode(Request::url())]);
        }
        User::setUcode();   //服务端缓冲邀请码
        //设置常用参数
        $this->member_miniapp        = $this->miniapp;
        $this->member_miniapp_id     = $this->miniapp->id;
        $this->member                = $this->miniapp->member;
        $this->service_id            = $this->miniapp->service_id;
        $this->ucode                 = User::getUcode(); //邀请码
        //传参数到模板调用
        $assign['miniapp']           = $this->miniapp;
        $assign['member_miniapp_id'] = $this->member_miniapp_id;
        $assign['member']            = $this->member;
        $assign['user']              = $this->user;
        $assign['invite_code']       = $this->user ? $this->user['invite_code'] : '';
        $assign['ucode']             = $this->ucode;
        $this->assign($assign);
        $this->isAppTyes($this->member_miniapp->miniapp->types);
    }

     /**
     * 判断当前页面的后退网址
     * @access protected
     * @return boolean
     */
    protected function backUrl(){
        $url     = $this->request->param('backurl','','strip_tags'); 
        $referer = Request::header('referer');
        if (empty($url) && isset($referer)) {
            return $referer;
        }else{
            return empty($url) ? Request::root(true) : $url;
        }
    }
   
     /**
     * 接口认证
     * @return void
     */
    protected function apiAccess() {
        $appid = $this->request->param('app/d',0);
        return SystemMemberMiniapp::where(['id' => $appid,'is_lock' => 0])->field('id,appname,service_id,member_id,miniapp_id,create_time,update_time,mp_appid,miniapp_appid')->find();
    }
}
