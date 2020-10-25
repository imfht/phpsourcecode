<?php
namespace app\common\util;

class Sms_aliyun{

    /**
     * 阿里云短信接口
     * 注意,方法名必须是send
     * @param string $phone
     * @param string $msg
     * @return string|boolean
     */
    public static function send($phone='',$msg=''){
        if(!class_exists("\\plugins\\smsali\\Api")){
            return '短信接口不存在';
        }
        $obj = new \plugins\smsali\Api(config('webdb.sms_access_id'),config('webdb.sms_access_key'));
        $signName = config('webdb.sms_sign_name');        //签名,比如齐博
        $templateCode = config('webdb.sms_template');     //使用的模板,比如SMS_16830430
        $phoneNumbers = $phone;
        $templateParam = ['code'=>$msg,'name'=>$msg];
        $result = $obj->sendSms($signName, $templateCode, $phoneNumbers, $templateParam);
        if($result['Code']=='OK'){
            return true;
        }else{
            return $result['Message'] . ' ' . $result['Code'];
        }
    }
	
}