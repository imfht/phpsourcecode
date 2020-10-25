<?php
namespace app\common\util;

class Weixin_share
{
    private $appId;
    private $appSecret;
    private $url;

    public function __construct ($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    public function getSignPackage ($url="")
    {
        $jsapiTicket = $this->getJsApiTicket();
        

        // 注意 URL 一定要动态获取，不能 hardcode.
//         $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ||  $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
//         // $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//         $PHP_SELF_TEMP = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
//         $_SERVER['QUERY_STRING'] && $PHP_SELF_TEMP .= "?" . $_SERVER['QUERY_STRING'];
//         $PHP_SELF = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $PHP_SELF_TEMP;
//         $url = $protocol . $_SERVER['HTTP_HOST'] . $PHP_SELF;
        $url = $url?:get_url('location');
        
        
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        
        $signature = sha1($string);
        
        $signPackage = array(
                "appId" => $this->appId,
                "nonceStr" => $nonceStr,
                "timestamp" => $timestamp,
                "url" => $url,
                "signature" => $signature,
                "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr ($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i ++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket ()
    {
        $accessToken = wx_getAccessToken();
        $ticket = cache('weixin_jsdk_ticket'.substr($accessToken,0,5));
        if (!$ticket) {
            //$accessToken = wx_getAccessToken();
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=' . $accessToken;
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                cache('weixin_jsdk_ticket'.substr($accessToken,0,5), $ticket, 1800);
            }
        }
        return $ticket;
    }

    private function httpGet ($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}

