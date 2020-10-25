<?php
namespace app\index\controller\wxapp;


use app\common\controller\IndexBase;

//小程序  
class Weixin extends IndexBase{    
    public function getconfig($url=''){
        if(config('webdb.weixin_type')<2 || config('webdb.weixin_appid')=='' || config('webdb.weixin_appsecret')==''){
            return $this->err_js('没有配置认证服务号');
        }
        if(!in_weixin()){
            return $this->err_js('不在微信中');
        }
        $jssdk = new \app\common\util\Weixin_share(config('webdb.weixin_appid'),config('webdb.weixin_appsecret'));
        $array = $jssdk->GetSignPackage($url);
        unset($array["rawString"]);
        return $this->ok_js($array);
    }
}
