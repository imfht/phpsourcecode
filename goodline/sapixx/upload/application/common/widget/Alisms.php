<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 阿里云短信短信服务
 */
namespace app\common\widget;
use app\common\model\ConfigApis;
use app\system\model\MemberBank;
use app\system\model\MemberBankBill;
use think\facade\Session;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

class Alisms{

    const sms_session_scope = 'sapixx#com/alisms/scope';  //SESSION作用域
    const sms_session_name  = 'sapixx#com/alisms/value';  //SESSION值

    /**
     * 默认短信发送接口
     */
    public function putSms($phone_id,$member_id = 0,$appname = null){
        //手机号验证码
        $info = ConfigApis::config('alisms');
        if(!$info){
            return ['code'=>0,'message'=>'服务商未配置短信服务'];
        }
        $data['product']  = empty($appname) ? $info['product'] : $appname;
        $data['phone_id'] = $phone_id;
        $data['code']     = getcode(6);
        if($member_id > 0 && !empty($info['price'])){
            $rel = MemberBank::moneyJudge($member_id,$info['price']); //判断余额
            if($rel){
                return ['code'=>0,'message'=>'帐号余额不足,请联系应用服务商'];
            }
            MemberBankBill::create(['state' => 1,'money' => $info['price'],'member_id' => $member_id,'message'=> '发送短信费用','update_time' => time()]);
            MemberBank::moneyUpdate($member_id,-$info['price']);
        }
        //开启调试,面发送本地调试
        if(config('app_debug')){
            self::setSms($data);
            return ['code'=>200,'message'=>"已发送[{$data['code']}]"];
        }
        $sendSms = new SendSms;
        $sendSms->setPhoneNumbers($phone_id);
        $sendSms->setSignName($info['sign_name']);
        $sendSms->setTemplateCode($info['tpl_id']);
        $sendSms->setTemplateParam($data);
        $sendSms->setOutId(date('YmdHis'));
        $config  = ['accessKeyId' => $info['aes_key'],'accessKeySecret' => $info['secret']];
        $client  = new Client($config);
        $result  = $client->execute($sendSms);
        if($result->Code == 'OK'){
            self::setSms($data);
            return ['code'=>200,'message'=>'已发送,10分钟内有效'];
        }else{
            return ['code'=>0,'message'=>$result->Message];
        }
    }
    

    /**
     * 设置验证码
     * @param  string $phone_id 验证手机号
     * @param  string $sms_code 验证码验证码
     */
    public function setSms(array $data){
        Session::set(self::sms_session_name,$data,self::sms_session_scope);
        return true;
    }

    /**
     * 判断验证码
     * @param  string $phone_id 验证手机号
     * @param  string $sms_code 验证码验证码
     */
    public function isSms($phone_id,$sms_code){
        if(!Session::has(self::sms_session_name,self::sms_session_scope)){
            return false;
        }
        $smscode = Session::get(self::sms_session_name,self::sms_session_scope);
        if($smscode['phone_id'] != $phone_id || $smscode['code'] != $sms_code){
            return false;
        }
        Session::clear(self::sms_session_scope);
        return true;
    }
}