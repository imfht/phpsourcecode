<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 微信小程序统一服务
 */
namespace app\common\widget;
use app\common\widget\WechatMp;
use app\common\model\MemberMiniappToken;
use app\common\model\MemberMiniapp;
use EasyWeChat\Factory;  //微信公众号
use encrypter\Encrypter;

class WechatProgram{

    /**
     * #######################################
     * 判断小程序是独立应用还是平台应用
     * @param integer $id小程序服务ID
     * @return boolean
     */
    public function isTypes(int $id){
        $program = MemberMiniapp::where(['id' => $id])->find();
        return empty($program->miniapp->is_openapp) ? self::openMiniProgram($id,$program) : self::miniProgram($id,$program);
    } 

    /**
     * 微信小程序配置(独立应用)
     * @param integer $id  来自用户应用ID
     * @return void
     */
    public function miniProgram(int $id,$miniapp = []){
        if (empty($miniapp)) {
            $miniapp = MemberMiniapp::where(['id' => $id])->field('miniapp_appid,miniapp_secret')->find();
        }
        $config = [
            'app_id' => $miniapp['miniapp_appid'],
            'secret' => $miniapp['miniapp_secret'],
        ];
        return Factory::miniProgram($config);;
    }

    /**
     * 微信小程序配置(开放平台)
     * @param integer $id  来自用户应用ID
     * @param array   $miniapp  //当前小程序配置
     * @return void
     */
    public function openMiniProgram(int $id,$miniapp = []){
        if(empty($miniapp)){
            $miniapp = MemberMiniapp::where(['id' => $id])->field('miniapp_appid')->find();
            if(!$miniapp || empty($miniapp['miniapp_appid'])){
                return false;
            }
        }
        if(empty($miniapp['miniapp_appid'])){
            return false;
        }
        $refreshToken = MemberMiniappToken::refreshToken($id,$miniapp['miniapp_appid']);
        if(!$refreshToken){
            return false;
        }
        $wechat = new WechatMp();
        return $wechat->openConfig()->miniProgram($miniapp['miniapp_appid'],$refreshToken['refreshToken']);
    }


    /**
     * #######################################
     * 生成小程序用户认证的token
     * @param $openid
     * @return string
     */
    public function createToken(array $param){
        if(empty($param['miniapp_uid']) || empty($param['miniapp_id']) || empty($param['uid'])){
            return false;
        }
        $data['miniapp_uid'] = $param['miniapp_uid'];
        $data['miniapp_id']  = $param['miniapp_id'];
        $data['uid']         = $param['uid'];
        return Encrypter::cpEncode(base64_encode(json_encode($data)),$param['service_id']);
    }

    /**
     * 验证用户认证的token
     * @param $openid
     * @return string
     */
    public function checkToken(array $param){
        if(empty($param['service_id']) || empty($param['token'])){
            return false;
        }
        $token_code = Encrypter::cpDecode($param['token'],$param['service_id']);
        $token = json_decode(base64_decode($token_code),true); 
        return empty($token) ? false : $token;
    }
}