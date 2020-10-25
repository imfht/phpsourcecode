<?php
/**
 * @copyright   Copyright (c) 2018 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 *  浏览器基类
 */
namespace app\common\controller;
use think\Controller;
use app\common\model\SystemWeb;
use app\common\model\SystemMemberMiniapp;
use filter\Filter;

class Base extends Controller{

    protected $web;           //站点参数
    protected $token        = 0; //公众号
    protected $is_mp        = false; //公众号
    protected $is_lightapp  = false; //应应用
    protected $is_minapp    = false; //小程序
    
    protected function initialize(){
        $this->web = SystemWeb::config();  //当前站点配置
        $this->assign(['web'=> $this->web]);
    }
    
    /**
     * 方法不存在
     */
    public function _empty(){
        return $this->error('访问的页面不存在');
    }

    /**
     * 判断小程序类型
     * @param string $miniapp
     * mp
     * program
     * app
     * mp_program
     * mp_program_app
     * @return boolean
     */
    protected function isAppTyes($apptyes){
        switch ($apptyes) {
            case 'mp':
                $this->is_mp    = true;
                break;
            case 'program':
                $this->is_weapp = true;
                break;
            case 'app':
                $this->is_lapp  = true;
                break;
            case 'mp_program':
                $this->is_mp    = true;
                $this->is_weapp = true;
                break;
            case 'mp_program_app':
                $this->is_mp    = true;
                $this->is_lapp  = true;
                $this->is_weapp = true;
                break;
        }
    }

    /**
     * 接口认证
     * @return void
     */
    protected function apiAccess() {
        $appid     = $this->request->param('app/d',0);
        $condition = [];
        if(empty($appid)){
            $header = $this->request->header();
            if(empty($header['request-miniapp']) && empty($header['request-time'])){
                return false;
            }
            $condition['service_id'] = Filter::filter_escape($header['request-miniapp']);
        }else{
            $condition['id'] = (int)$appid;
        }
        $condition['is_lock'] = 0;
        $this->token = $header['request-token'] ?? 0;
        return SystemMemberMiniapp::where($condition)->field('id,member_id,appname,service_id,navbar_color,navbar_style,create_time,update_time,miniapp_appid,miniapp_id,mp_appid,is_psp')->find();
    }
}
