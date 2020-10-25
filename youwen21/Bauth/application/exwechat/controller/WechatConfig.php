<?php

namespace app\exwechat\controller;

use youwen\exwechat\api\accessToken;

/**
 * 微信配置 － 微信基础资料生成
 * @author baiyouwen <youwen21@yeah.net>
 * 如果只是单个微信公众号 也可能把appid  secret入到配置文件中 直接config取也好
 */
class WechatConfig
{
    public $appid = 'wx70fe57dfaad1a35f';
    public $secret = '62df1a5d360ffbe8c8a305b5a712f61e';
    public $encodingAesKey='da8yIZvckoaMVVpbOPo7YN0KdOa71DataVoan4Tmq2i';
    public $token='bjnqCQ1440899050';

    // 欠周到
    // public function __construct($FormUserName='')
    // {
    // 	// get appid/secret  by  FormUserName
    //     // 公众号不多的情况下 可以把FormUserName 和 appid 的对应关系 写到配置中 
    // }

    public function getAccessToken()
    {
    	// get access_token  by appid  secret  
        // 实现access_token的缓存 
        // $access = new accessToken($appid, $appsecret);
        // $token = $access->getAccessToken();
        // exit(json_encode($token));
    }
}
