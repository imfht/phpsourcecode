<?php

/**
 * SESSION操作
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Session
{
    public function __construct()
    {
        if (session_id() == '') {
            session_start();
        }
    }

    public static function set($key, $val = '')
    {
        return $_SESSION[$key] = $val;
    }

    public static function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    public static function del($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return true;
        } else {
            return false;
        }
    }
}

?>