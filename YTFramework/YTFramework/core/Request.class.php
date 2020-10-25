<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: Request.class.php 89 2016-04-21 02:53:46Z lixiaomin $
 *  @created    2015-10-10
 *  Request
 * =============================================================================                   
 */
namespace core;
class Request
{

    protected static $request;

    public static function set($k, $v)
    {
        self::$request[$k] = $v;
    }

    public static function get($method = '', $k = '')
    {
        if (empty(self::$request[$method])) {
            return null;
        }

        if (empty($k)) {
            return self::$request[$method];
        }

        return isset(self::$request[$method][$k]) ? self::$request[$method][$k] : null;
    }

    public static function isPost()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? 1 : 0;
    }

    public static function isAjax()
    {
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
            return true;
        }
        return false;
    }

    public static function isGet()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'GET' && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? 1 : 0;
    }

}
