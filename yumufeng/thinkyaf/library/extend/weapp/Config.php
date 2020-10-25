<?php
/**
 * Created by PhpStorm.
 * User: yingouqlj
 * Date: 17/1/17
 * Time: 下午1:49
 */

namespace extend\weapp;


use think\Cache;

class Config
{
    public $appId;
    public $secret;
    public $accessToken;
    const ACCESS_TOKEN_REDIS_KEY = 'weapp_access_token';

    public function __construct($config = null)
    {

        if (empty($config)) {
            $config = \Config::get('weapp');
        }
        if (isset($config['appid'])) {
            $this->appId = $config['appid'];
        }
        if (isset($config['ak'])) {
            $this->secret = $config['ak'];
        }
    }

    /**
     * 覆盖这个方法写取token的实现 ，比如redis，数据库
     * @return $token
     */
    public function getAccessToken()
    {
        $token = Cache::get(self::ACCESS_TOKEN_REDIS_KEY);
        if (empty($token)) {
            return null;
        }
        return $token;
    }

    /**
     * 覆盖这个方法 存token，默认写临时文件
     * @param $token
     * @param int $expires
     * @return int
     */
    public function setAccessToken($token, $expires = 0)
    {
        $setData = Cache::set(self::ACCESS_TOKEN_REDIS_KEY, $token, $expires);
        return $setData;
    }


}