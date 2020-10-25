<?php

namespace App\Plugin;

use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/27
 * Time: 13:30
 */
class WxJssdk
{
    private $appId;
    private $appSecret;
    private $appUrl;

    public function __construct($appId, $appSecret, $appUrl = '')
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->appUrl    = $appUrl;
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();
        if (!$this->appUrl) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url      = "{$protocol}{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        } else {
            $url = $this->appUrl;
        }
        $timestamp = time();
        $nonceStr  = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string    = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
        $signature = sha1($string);
        $signPackage = array (
            "appid"     => $this->appId,
            "noncestr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket()
    {
        $cache_key = 'wx_jsapi_ticket';
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        if (!cache()->has($cache_key)) {
            $accessToken = $this->getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token={$accessToken}";
            $res = json_decode($this->httpGet($url));
            if (isset($res->errcode) && $res->errcode == 40001) {
                $this->getJsApiTicket(true);
                exit;
            }
            if (isset($res->ticket)) {
                cache([$cache_key => $res], Carbon::now()->addHour(2));
            }
        }
        $data = cache($cache_key);
        return $data ? $data->ticket : false;
    }

    private function getAccessToken()
    {
        $cache_key = 'wx_access_token';
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        if (!cache()->has($cache_key)) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}";
            $res = json_decode($this->httpGet($url));
            if (isset($res->access_token)) {
                $expired = Carbon::now()->addHour(2);
                cache([$cache_key => $res], $expired);
            }
        }
        $data = cache($cache_key);
        return $data ? $data->access_token : false;
    }

    private function httpGet($url)
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