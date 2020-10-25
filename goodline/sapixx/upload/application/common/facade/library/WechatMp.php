<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 微信公众号统一处理
 */
namespace app\common\facade\library;
use app\common\model\SystemMemberMiniappToken;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemApis;
use EasyWeChat\Factory;  //微信公众号
use Exception;

class WechatMp{

     /**
     * 微信开放平台配置 https://open.weixin.qq.com
     * @return void
     */
    public function openConfig(){
        $info = SystemApis::config('wechatopen');
        $config = [
            'app_id'   => $info['app_id'],
            'secret'   => $info['secret'],
            'token'    => $info['token'],
            'aes_key'  => $info['aes_key']
        ]; 
        return Factory::openPlatform($config);
    }

    /**
     * #######################################
     * 编译生成JSSDK配置
     * @param array $jsApiList
     * @return json
     */
    public function jsApiList(int $app,$jsApiList = []){
        if(empty($jsApiList)){
            $jsApiList = ["checkJsApi","invokeMiniProgramAPI","launchMiniProgram","hideMenuItems",'showMenuItems','hideAllNonBaseMenuItem','showAllNonBaseMenuItem','scanQRCode',"onMenuShareTimeline","onMenuShareAppMessage","closeWindow","getNetworkType","previewImage","onVoiceRecordEnd","onVoicePlayEnd",'chooseWXPay','chooseCard','openCard','addCard','openAddress','chooseImage','previewImage','uploadImage','downloadImage','getLocalImgData'];
        }
        return self::isTypes($app)->jssdk->buildConfig($jsApiList,false);
    }

    /**
     * #######################################
     * 判断小程序是独立应用还是平台应用
     * @param integer $id小程序服务ID
     * @return boolean
     */
    public function isTypes(int $id){
        try {
            $program = SystemMemberMiniapp::where(['id' => $id])->find();
            return empty($program->miniapp->is_openapp) ? self::openOfficial($id,$program) : self::official($id,$program);
        }catch (Exception $e) {
            return;
        }
    } 

    /**
     * 微信基础配置
     * @param integer $id = 0 是读取后台系统的公众号配置
     */
    public function official(int $id = 0,$miniapp = []){
        $config = [];
        if ($id) {
            if (empty($miniapp)) {
                $miniapp = SystemMemberMiniapp::where(['id' => $id])->field('mp_appid,mp_secret')->find();
            }
            $config = ['app_id' => $miniapp['mp_appid'],'secret' => $miniapp['mp_secret']];
        }else{
            $miniapp = SystemApis::config('wechataccount');
            $config = ['app_id'  => $miniapp['app_id'],'secret'  => $miniapp['secret'],'token'   => $miniapp['token'],'aes_key' => $miniapp['aes_key']];
        }
        if(empty($config['app_id']) || empty($config['secret'])){
            return false;
        }
        return Factory::officialAccount($config);
    }

     /**
     * 微信开放平台配置
     * @return void
     */
    public function openOfficial(int $id,$miniapp = []){
        if(empty($miniapp)){
            $miniapp = SystemMemberMiniapp::where(['id' => $id])->field('mp_appid')->find();
        }
        if(empty($miniapp['mp_appid'])){
            return false;
        }
        $refreshToken = SystemMemberMiniappToken::refreshToken($id,$miniapp['mp_appid']);
        if(empty($refreshToken)){
            return false;
        }
        return self::openConfig()->officialAccount($miniapp['mp_appid'],$refreshToken['refreshToken']);
    }
}