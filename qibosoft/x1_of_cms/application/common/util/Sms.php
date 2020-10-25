<?php
namespace app\common\util;

class Sms{
    
    /**
     * 更换其它短信接口的话,请参考下面的方法,不需要修改本程序,否则升级会被替换
     * @param string $phone 手机号码
     * @param string $msg 短信内容
     * @return boolean|string
     */
    public function send($phone='',$msg=''){
        static $obj = null;
        if(preg_match("/^(17134)/", $phone)){
            return '请更换一个手机号,当前手机号已被列入了黑名单';
        }
        if (config('webdb.sms_type')!='' && config('webdb.sms_type')!='aliyun') {
            $class_name = "plugins\\".config('webdb.sms_type')."\\Api";
            if ( class_exists($class_name) && method_exists($class_name, 'send') ) {
                if($obj===null){
                    $obj = new $class_name();
                }
                return $obj->send($phone,$msg);
            }else{
                $class_name = "app\\common\\util\\Sms_".config('webdb.sms_type');
                if ( class_exists($class_name) && method_exists($class_name, 'send') ) {
                    if($obj===null){
                        $obj = new $class_name();
                    }                    
                    return $obj->send($phone,$msg);
                }
            }
        }        
        return Sms_aliyun::send($phone,$msg);
    }

}