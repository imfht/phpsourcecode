<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/23/16
 * Time: 5:49 PM
 */
include_once 'PhoneUtils.php';
class PhoneMsg{
    const MSG_SERVER='app.cloopen.com';//请求地址，格式如下，不需要写https://
    const MSG_PORT='8883';//请求端口
    const MSG_TEMPID='';//模板id
    const MSG_VERSION='2013-12-26';//REST版本号
    const MSG_ID='';//主帐号
    const MSG_APPID='';//应用Id
    const MSG_TOKEN='';//主帐号Token
    /**
     * 发送短信
     * @param $to  手机号
     * @param $datas send 需要发送的数据
     * @return bool success true  fail false
     */
    public static function send($to,$datas){
        $rest=new PhoneUtils(self::MSG_SERVER,self::MSG_PORT,self::MSG_VERSION);

        $rest->setAccount(self::MSG_ID,self::MSG_TOKEN);
        $rest->setAppId(self::MSG_APPID);
        $result = $rest->sendTemplateSMS($to,$datas,self::MSG_TEMPID);
        return true;
        /* if($result == NULL ) {
             return false;
         }
         if($result->statusCode!=0) {
             return false;
         }else{
             return true;
         }*/
    }
}