<?php

use Kernel\Loader;
use Kernel\Config;
use Kernel\Cache;
use Kernel\View;

/* 基础 */
if (!function_exists('singleton')) {

    function singleton($class,$layer='',$boot='App')
    {
        $className = $boot.'\\'.$layer.'\\'.$class;
        return Loader::singleton($className);
    }

}

if (!function_exists('config')) {

    function config($name = '', $value = null, $range = '')
    {
        $config = Config::instance();
        if (is_null($value) && is_string($name)) {
            return 0 === strpos($name, '?') ? $config->has(substr($name, 1), $range) : $config->get($name, $range);
        } else {
            return $config->set($name, $value, $range);
        }
    }

}

if (!function_exists('cache')) {

    function cache($name, $value = '', $expire=null , $tag = null)
    {
        $cache = Cache::instance();
        if ('' === $value) {
            // 获取缓存
            return 0 === strpos($name, '?') ? $cache->has(substr($name, 1)) : $cache->get($name);
        } elseif (is_null($value)) {
            // 删除缓存
            return $cache->rm($name);
        } else {
            // 缓存数据
            if (is_null($tag)) {
                return $cache->set($name, $value, $expire);
            } else {
                return $cache->tag($tag)->set($name, $value, $expire);
            }
        }
    }

}


/* 工具 */
if (!function_exists('parse_class_name')) {

    function parse_class_name($name, $type = 0)
    {
        if ($type) {
            return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name));
        } else {
            return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
    }
    
}

if (!function_exists('is_session_started')) {

    function is_session_started()
    {
        if ( php_sapi_name() !== 'cli' ) {
            if ( version_compare(phpversion(), '5.4.0', '>=') ) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }

}

/* 综合 */
function dd($value)
{
    var_dump($value);
}
function value($name,$default='',$filter="")
{
    if(!isset($$name)) return $default;
    if(!empty($filter)){
        return call_user_func($filter,$$name);
    }else{
        return $$name;
    }
}
function show_404()
{
    header('HTTP/1.1 404 Not Found');
    die( '404 Not Found' );
}
function show_500()
{
    header('HTTP/1.1 500 Internal Server Error');
    die( '500 Internal Server Error' );
}