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

class Session
{
    /**
     * 是否初始化
     * @var null
     */
    protected static $init = null;

    /**
     * 初始化Session
     */
    public static function init()
    {
        if (is_null(self::$init)) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            self::$init = true;
        }
    }

    /**
     * 设置Session
     * @param $name string|array Session值|['session名' => 'session值']
     * @param string $value Session值
     */
    public static function set($name, $value = '')
    {
        empty(self::$init) && self::init();

        if (is_string($name)) {
            $_SESSION[$name] = $value;
        }

        if (is_array($name)) {
            foreach ($name as $item => $val) {
                $_SESSION[$item] = $val;
            }
        }
    }

    /**
     * 获取Session
     * @param string $name 为空获取所有Session
     * @return bool|array|string
     */
    public static function get($name = '')
    {
        empty(self::$init) && self::init();

        return empty($name) ? $_SESSION : (isset($_SESSION[$name]) ? $_SESSION[$name] : false);
    }

    /**
     * 判断Session是否设置
     * @param $name Session名
     * @return bool
     */
    public static function has($name)
    {
        empty(self::$init) && self::init();

        return isset($_SESSION[$name]) ? true : false;
    }

    /**
     * 取出Session值并删除
     * @param $name
     * @return array|bool|string
     */
    public static function pull($name)
    {
        empty(self::$init) && self::init();

        $session = self::get($name);
        if ($session) {
            self::delete($name);
            return $session;
        }
        return false;
    }

    /**
     * 删除Session
     * @param $name Session名
     * @return bool
     */
    public static function delete($name)
    {
        empty(self::$init) && self::init();

        $session = self::get($name);
        if (is_string($session)) {
            unset($_SESSION[$name]);
        }

        if (is_array($session)) {
            $_SESSION[$name] = [];
        }
        return true;
    }

    /**
     * 清空Session
     */
    public static function clear()
    {
        empty(self::$init) && self::init();

        $_SESSION = [];
    }

    /**
     * 启动session
     * @return void
     */
    public static function start()
    {
        session_start();
        self::$init = true;
    }

    /**
     * 销毁session
     * @return void
     */
    public static function destroy()
    {
        if (!empty($_SESSION)) {
            $_SESSION = [];
        }
        session_unset();
        session_destroy();
        self::$init = null;
    }

    /**
     * 暂停session
     * @return void
     */
    public static function pause()
    {
        // 暂停session
        session_write_close();
        self::$init = false;
    }
}