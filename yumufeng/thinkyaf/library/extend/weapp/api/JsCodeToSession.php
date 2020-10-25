<?php
/**
 * Created by PhpStorm.
 * User: yingouqlj
 * Date: 17/1/13
 * Time: 下午5:15
 */

namespace extend\weapp\api;

class JsCodeToSession extends BaseApi
{
    const API = 'https://api.weixin.qq.com/sns/jscode2session';
    protected $grantType = 'authorization_code';
    protected $jsCode;
    public $openid;
    public $sessionKey;


    /**
     * @param $code
     * @return $this
     */
    public function getSession($code)
    {
        $this->jsCode = $code;
        $params = [
            'appid' => $this->appId,
            'secret' => $this->secret,
            'js_code' => $this->jsCode,
            'grant_type' => $this->grantType,
        ];
        $result = $this->makeRequest(self::API, $params);
        return $result;

    }


}