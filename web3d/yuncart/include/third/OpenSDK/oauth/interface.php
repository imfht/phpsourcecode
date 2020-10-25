<?php

/**
 * OAuth1.0 SDK Interface
 *
 * 提供给具体接口子类使用的一些公共方法
 *
 * @author icehu@vip.qq.com
 */
class OpenSDK_OAuth_Interface
{

    const RETURN_JSON = 'json';
    const RETURN_XML = 'xml';

    protected static $timestampFunc = null;

    /**
     * 获得本机时间戳的方法
     * 如果服务器时钟存在误差，在这里调整
     *
     * @return number
     */
    public static function getTimestamp()
    {
        if (null !== self::$timestampFunc && is_callable(self::$timestampFunc)) {
            return call_user_func(self::$timestampFunc);
        }
        return time();
    }

    /**
     * 设置获取时间戳的方法
     *
     * @param function $func
     */
    public static function timestamp_set_save_handler($func)
    {
        self::$timestampFunc = $func;
    }

    protected static $getParamFunc = null;

    /**
     *
     * 获取OAuth交互Session参数
     * 可以使用 param_set_save_handler 设置自定义的获取方法来覆盖默认的$_SESSION方法
     *
     * @param string $key Session key
     * @return string
     */
    public static function getParam($key)
    {
        if (null !== self::$getParamFunc && is_callable(self::$getParamFunc)) {
            return call_user_func(self::$getParamFunc, $key);
        }
        return isset($_SESSION['oauth'][$key]) ? $_SESSION['oauth'][$key] : null;
    }

    /**
     *
     * 设置Session数据的存取方法
     * 类似于session_set_save_handler来重写Session的存取方法
     * 当你的token存储到跟用户相关的数据库中时非常有用
     *
     * $get方法 接受1个参数 $key
     * $set方法 接受2个参数 $key $val
     *
     * @param function|callback $get
     * @param function|callback $set
     */
    public static function param_set_save_handler($get, $set)
    {
        self::$getParamFunc = $get;
        self::$setParamFunc = $set;
    }

    protected static $setParamFunc = null;

    /**
     *
     * 设置OAuth交互Session参数
     * 可以使用 param_set_save_handler 设置自定义的获取方法来覆盖默认的$_SESSION方法
     * 当$val为null时，表示删除该Session key的值
     *
     * @param string $key Session key
     * @param string $val Session val
     */
    public static function setParam($key, $val = null)
    {
        if (null !== self::$setParamFunc && is_callable(self::$setParamFunc)) {
            return call_user_func(self::$setParamFunc, $key, $val);
        }
        if (null === $val) {
            unset($_SESSION['oauth'][$key]);
            return;
        }
        $_SESSION['oauth'][$key] = $val;
    }

    protected static $remot_ip = null;

    /**
     * 设置内容生产者的IP
     * @param string $ip
     */
    public static function set_remote_ip($ip)
    {
        self::$remot_ip = $ip;
    }

    protected static function getRemoteIp()
    {
        return self::$remot_ip ? self::$remot_ip : $_SERVER['REMOTE_ADDR'];
    }

}
