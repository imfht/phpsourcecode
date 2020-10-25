<?php

namespace WechatOauthProxy;

class WechatOauth
{
    /**
     * 获取code
     * @param array $paramsArr
     * @param $oauthType
     */
    public static function toGetCode($paramsArr = array(),$oauthType)
    {
        $apiUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
        if ($oauthType == 2) {
            $apiUrl = 'https://open.weixin.qq.com/connect/qrconnect?';
        }
        $requestUrl = $apiUrl . http_build_query($paramsArr) . '#wechat_redirect';
        header('Location:' . $requestUrl);
    }

    /**
     * 获取access_token
     * @param array $paramsArr
     * @return mixed
     */
    public static function getAccessToken($paramsArr = array())
    {
        $apiUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
        $requestUrl = $apiUrl . http_build_query($paramsArr);
        return json_decode(WechatOauth::http_request($requestUrl),true);
    }

    /**
     * 刷新access_token
     * @param array $paramsArr
     * @return mixed
     */
    public static function refreshAccessToken($paramsArr = array())
    {
        $apiUrl = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?';
        $requestUrl = $apiUrl . http_build_query($paramsArr);
        return json_decode(WechatOauth::http_request($requestUrl),true);
    }

    /**
     * 校验access_token
     * @param array $paramsArr
     * @return mixed
     */
    public static function checkAccessToken($paramsArr = array())
    {
        $apiUrl = 'https://api.weixin.qq.com/sns/auth?';
        $requestUrl = $apiUrl . http_build_query($paramsArr);
        return json_decode(WechatOauth::http_request($requestUrl),true);
    }

    /**
     * curl进行http请求
     * @param $url
     * @param null $data
     * @return mixed
     */
    public static function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}