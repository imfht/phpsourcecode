<?php

namespace fluiex\web;

class Cookie
{

    public static function set($var, $value = '', $life = 0, $prefix = 1, $httponly = false)
    {

        global $_G;

        $config = $_G['config']['cookie'];

        $_G['cookie'][$var] = $value;
        $var = ($prefix ? $config['cookiepre'] : '') . $var;
        $_COOKIE[$var] = $value;

        if ($value == '' || $life < 0) {
            $value = '';
            $life = -1;
        }

        if (defined('IN_MOBILE')) {
            $httponly = false;
        }

        $life = $life > 0 ? getglobal('timestamp') + $life : ($life < 0 ? getglobal('timestamp') - 31536000 : 0);
        $path = $httponly && PHP_VERSION < '5.2.0' ? $config['cookiepath'] . '; HttpOnly' : $config['cookiepath'];

        $secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
        if (PHP_VERSION < '5.2.0') {
            setcookie($var, $value, $life, $path, $config['cookiedomain'], $secure);
        } else {
            setcookie($var, $value, $life, $path, $config['cookiedomain'], $secure, $httponly);
        }
    }

    public static function get($key)
    {
        global $_G;
        
        return isset($_G['cookie'][$key]) ? $_G['cookie'][$key] : '';
    }

}
