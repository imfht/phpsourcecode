<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 3:59 PM
 */

namespace App\Services;

use http\Env\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class TokenService
{
    private $token_prefix = '';
    private $expire_time = '';
    private $token_name = '';

    public function __construct($api_type = 'api')
    {
        $this->token_prefix = $api_type . '_token:';
        $this->expire_time = 3600 * 2;
        if ($api_type == 'api') {
            $this->expire_time = 3600 * 24 * 30;
        }
    }

    /**
     * 生成token
     * @param $data 用户数据
     * @return string
     */
    public function setToken($data = array())
    {
        if (!$data['id']) return false;
        $str = Str::random(20) . $data['id'] . time();
        $token = md5($str);
        $redis_key = $this->tokenKey($token);
        Redis::set($redis_key, json_encode($data));
        Redis::expire($redis_key, $this->expire_time);
        return $token;
    }

    /**
     * 获取token
     * @return string
     */
    public function getToken($token_name = '')
    {
        $token_data = Redis::get($this->tokenKey($token_name));
        if ($token_data) {
            $token_data = json_decode($token_data, true);
        }
        return $token_data;
    }

    /**
     * 刷新token
     * @return bool
     */
    public function refreshToken($token_name = '')
    {
        $key = $this->tokenKey($token_name);
        Redis::expire($key, $this->expire_time);
        return true;
    }

    /**
     * 删除token
     * @return string
     */
    public function delToken($token_name = '')
    {
        $key = $this->tokenKey($token_name);
        Redis::del($key);
        return true;
    }

    /**
     * 获取key
     * @param $token
     * @return string
     */
    public function tokenKey($token_name = '')
    {
        if (!$token_name) {
            $token_name = $this->token_name;
            if (!$token_name) {
                $token_name = $this->getTokenName();
            }
        }
        $redis_key = $this->token_prefix . $token_name;
        return $redis_key;
    }

    /**
     * 获取token名称
     * @return array|null|string
     */
    public function getTokenName()
    {
        $token_name = request()->input('token');
        if (!$token_name) {
            $token_name = request()->cookie('token');
            if (!$token_name) {
                $token_name = request()->header('token');
            }
        }
        $this->token_name = $token_name;
        return $token_name;
    }
}