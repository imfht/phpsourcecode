<?php
namespace app\user\controller;
use think\Db;
use OauthSDK\Oauth as OauthC;
use think\Controller;
class Oauth extends Controller{
    public function login(){
        $type = input('type');
        if (!in_array($type, ['qq', 'wechat', 'sina'])) {
            throw new \Exception("暂不支持{$type}方式登录", 500);
        }
        $plugin =Db::name('plugin')->where(['type'=>'login','status'=>1])->select();
        foreach ($plugin as $k=>$v){
            $config[strtoupper($v['code'])]=unserialize($v['config_value']);
        }
        $sns = OauthC::getInstance($type, $config);
        //跳转到授权页面
        $this->redirect($sns->getRequestCodeURL());
    }

}