<?php

// +----------------------------------------------------------------------
// | HopePHP
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.wispx.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: WispX <i@wispx.cn>
// +----------------------------------------------------------------------

namespace hope;

class Cookie
{
    /**
     * 是否已初始化
     * @var null
     */
    protected static $init = null;

    /**
     * Cookie配置
     * @var null
     */
    protected static $config = null;

    /**
     * 初始化
     */
    public static function init()
    {
        if (is_null(self::$config)) {
            self::$config = Config::get('cookie');
            self::$init = true;
        }
    }

    /**
     * 设置Cookie
     * @param array|string $name Cookie名
     * @param string|array $value Cookie值
     */
    public static function set($name, $value = '', $expire = 0)
    {
        !isset(self::$init) && self::init();

        if ($expire !== 0) {
            $expire = !empty(self::$config['expire']) ? $_SERVER['REQUEST_TIME'] + intval(self::$config['expire']) : 0;
        }

        if (is_string($name)) {
            setcookie(
                $name, $value, $expire, self::$config['path'],
                self::$config['domain'], self::$config['secure']
            );
        }

        if (is_array($name)) {
            foreach ($name as $item => $val) {
                setcookie(
                    $name, $val, $expire, self::$config['path'],
                    self::$config['domain'], self::$config['secure']
                );
            }
        }
    }

    /**
     * 获取Cookie值
     * @param string $name 为空获取全部
     * @return bool
     */
    public static function get($name = '')
    {
        !isset(self::$init) && self::init();

        if ('' == $name) {
            return $_COOKIE;
        }
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : false;
    }

    /**
     * 判断Cookie是否设置
     * @param string $name Cookie名
     * @return bool
     */
    public static function has($name)
    {
        !isset(self::$init) && self::init();

        return isset($_COOKIE[$name]);
    }

    /**
     * 永久保存Cookie
     * @param string|array $name Cookie名
     * @param string|array $value Cookie值
     */
    public static function forever($name, $value = '')
    {
        !isset(self::$init) && self::init();

        self::set($name, $value, 315360000);
    }

    /**
     * 删除指定Cookie
     * @param $name Cookie 名
     */
    public static function delete($name)
    {
        !isset(self::$init) && self::init();

        setcookie(
            $name, '', $_SERVER['REQUEST_TIME'] - 3600, self::$config['path'],
            self::$config['domain'], self::$config['secure']
        );

        unset($_COOKIE[$name]);
    }

    /**
     * 清空Cookie
     */
    public static function clear()
    {
        if (empty($_COOKIE)) {
            return;
        }

        !isset(self::$init) && self::init();

        foreach ($_COOKIE as $key => $val) {

            setcookie(
                $key, '', $_SERVER['REQUEST_TIME'] - 3600, self::$config['path'],
                self::$config['domain'], self::$config['secure']
            );

            unset($_COOKIE[$key]);
        }
    }
}