<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/5
 * Time: 13:17
 */

namespace naples\lib;


use naples\lib\base\IGetSet;
use naples\lib\base\Service;

/**
 * Cookie服务类
 * 实现IGetSet接口
 * 配置encrypt实现自动加密(默认)
 */
class Cookie extends Service implements IGetSet
{
    /**
     * 获取cookie
     * @param $key string 键名
     * @return mixed 键值
     */
    function get($key){
        if (isset($_COOKIE[$key])){
            $value=$_COOKIE[$key];
            if ($this->config('encrypt')){
                $value=\Yuri2::decrypt($value,$this->config('key'));
                $value=unserialize($value);
            }
            return $value;
        }else{
            return null;
        }
    }

    /**
     * 设置cookie
     * @param $key string 键名
     * @param $value mixed 键值
     * @param $expiry int 剩余过期时间，默认1个月
     * @return mixed 实际保存的键值
     */
    function set($key,$value,$expiry=2592000){
        if ($this->config('encrypt')){
            $value=serialize($value);
            $value=\Yuri2::encrypt($value,$this->config('key'));
        }
        $expiry=TIMESTAMP+$expiry;
        setcookie($key,$value,$expiry);
        return $value;
    }

    /**
     * 是否有一个键值
     * @return bool
     */
    function has($key)
    {
        if (isset($_COOKIE[$key])){
            return true;
        }else{
            return false;
        }
    }

}