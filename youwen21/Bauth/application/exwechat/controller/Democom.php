<?php

namespace app\exwechat\controller;

use youwen\exwechat\api\accessToken;
use youwen\exwechat\api\account\QRCode;
use youwen\exwechat\api\account\shortUrl;
use youwen\exwechat\api\ips;

/**
 * 公众案例
 * 获取去access_token    ips
 * 生成二维码和短网址
 */
class Democom
{
    public function qrCode()
    {
        $class = new QRCode($_GET['token']);
        $ret = $class->temporaryQR($_GET['scene_id']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function shortUrl()
    {
        $class = new shortUrl($_GET['token']);
        $ret = $class->create($_GET['url']);
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function ips()
    {
        $token = $_GET['token'];
        $class = new ips($token);
        $ret = $class->getIps();
        echo '<pre>';
        print_r($ret);
        exit('</pre>');
    }

    public function accessToken()
    {
        $appid = 'wx70fe57dfaad1a35f';
        $appsecret = '62df1a5d360ffbe8c8a305b5a712f61e';

        $access = new accessToken($appid, $appsecret);
        $token = $access->getAccessToken();
        exit(json_encode($token));
        // echo '<pre>';
        // print_r($token);
        // exit('</pre>');
    }
}
