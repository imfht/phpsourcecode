<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 阿里云短信验证码
 */
namespace app\common\facade\library;
use app\common\model\SystemApis;
use app\common\model\SystemMemberBank;
use app\common\model\SystemMemberBankBill;
use think\facade\Session;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

class Alisms{

    
    const sms_session_scope = 'sapixx#com/alisms/scope';  //SESSION作用域
    const sms_session_name  = 'sapixx#com/alisms/value';  //SESSION值


    /**
     * 短信验证码发送
     *
     * @param integer $phone_id  接受手机号
     * @param integer $member_id  用户ID
     * @param string  $tpl_name 签名
     * @return void
     */
    public function putSms($phone_id,$member_id = 0,$tpl_name = ''){
        //手机号验证码
        $info = SystemApis::config('alisms');
        if(!$info){
            return ['code'=>0,'message'=>'服务商未配置短信服务'];
        }
        $data['phone_id'] = $phone_id;
        $data['code']     = getcode(6);
        if($member_id > 0 && !empty($info['price'])){
            $rel = SystemMemberBank::moneyJudge($member_id,$info['price']); //判断余额
            if($rel){
                return ['code'=>0,'message'=>'帐号余额不足,请联系应用服务商'];
            }
            SystemMemberBankBill::create(['state' => 1,'money' => $info['price'],'member_id' => $member_id,'message'=> '发送短信费用','update_time' => time()]);
            SystemMemberBank::moneyUpdate($member_id,-$info['price']);
        }
        //开启调试,面发送本地调试
        if(config('app_debug')){
            self::setSms($data);
            return ['code'=>200,'message'=>"已发送[{$data['code']}]"];
        }
        $sendSms = new SendSms;
        $sendSms->setPhoneNumbers($phone_id);
        $sendSms->setSignName(empty($tpl_name) ? $info['sign_name'] : $tpl_name);
        $sendSms->setTemplateCode($info['tpl_id']);
        $sendSms->setTemplateParam($data);
        $sendSms->setOutId(date('YmdHis'));
        $client  = new Client(['accessKeyId' => $info['aes_key'],'accessKeySecret' => $info['secret']]);
        $result  = $client->execute($sendSms);
        if($result->Code == 'OK'){
            self::setSms($data);
            return ['code'=>200,'message'=>'已发送,10分钟内有效'];
        }else{
            return ['code'=>0,'message'=>$result->Message];
        }
    }


    /**
     * 默认短信发送接口
     *
     * @param [type] $phone_id    接受短信的手机号
     * @param [type] $data        短信变量数据   $data['sign_name'] = '应用签名'
     * @param [type] $tpl_id      短信模板ID
     * @param integer $member_id  用户ID
     * @return void
     */
    public function sendSms($phone_id,$data,$tpl_id,$member_id = 0){
        //手机号验证码
        $info = SystemApis::config('alisms');
        if(!$info){
            return ['code'=>0,'message'=>'服务商未配置短信服务'];
        }
        if($member_id > 0 && !empty($info['price'])){
            $rel = SystemMemberBank::moneyJudge($member_id,$info['price']); //判断余额
            if($rel){
                return ['code'=>0,'message'=>'帐号余额不足,请联系应用服务商'];
            }
            SystemMemberBankBill::create(['state' => 1,'money' => $info['price'],'member_id' => $member_id,'message'=> '发送短信费用','update_time' => time()]);
            SystemMemberBank::moneyUpdate($member_id,-$info['price']);
        }
        $tpl_name = empty($data['sign_name']) ? $info['sign_name'] : $data['sign_name'];
        unset($data['sign_name']);
        $sendSms = new SendSms;
        $sendSms->setPhoneNumbers($phone_id);
        $sendSms->setSignName($tpl_name);
        $sendSms->setTemplateCode($tpl_id);
        $sendSms->setTemplateParam($data);
        $sendSms->setOutId(date('YmdHis'));
        $client  = new Client(['accessKeyId' => $info['aes_key'],'accessKeySecret' => $info['secret']]);
        $result  = $client->execute($sendSms);
        if($result->Code == 'OK'){
            return ['code'=>200,'message'=>'已发送'];
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