<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:03
 */

use fastwork\Container;
use fastwork\facades\Config;
use fastwork\facades\Cookie;
use fastwork\facades\Env;
use fastwork\facades\Session;

if (!function_exists('app')) {
    /**
     * 快速获取容器中的实例 支持依赖注入
     * @param string $name 类名或标识 默认获取当前应用实例
     * @param array $args 参数
     * @param bool $newInstance 是否每次创建新的实例
     * @return object
     */
    function app($name = 'fastwork', $args = [], $newInstance = false)
    {
        return Container::get($name, $args, $newInstance);
    }
}

/**
 * 生成UUID
 * @param bool $base62
 * @return string
 * @throws Exception
 */
function uuid($base62 = true)
{
    $str = uniqid('', true);
    $arr = explode('.', $str);
    $str = $arr[0] . base_convert($arr[1], 10, 16);
    $len = 32;
    while (strlen($str) <= $len) {
        $str .= bin2hex(random_bytes(4));
    }
    $str = substr($str, 0, $len);
    if ($base62) {
        $str = str_replace(['+', '/', '='], '', base64_encode(hex2bin($str)));
    }
    return $str;
}

/**
 * 统一格式json输出
 */
function format_json($data, $code, $id = null)
{
    $arr = ['code' => $code];
    if ($code) {
        $arr['msg'] = $data;
    } else {
        $arr['msg'] = '';
        $arr['data'] = $data;
    }
    if ($id) {

    }
    return json_encode($arr, JSON_UNESCAPED_UNICODE);
}

/**
 * 从数组中根据key取出值
 * @param array $arr
 * @param $key
 * @return mixed|null
 */
function array_get($arr, $key, $default = null)
{
    if (isset($arr[$key])) {
        return $arr[$key];
    } else if (strpos($key, '.') !== false) {
        $keys = explode('.', $key);
        foreach ($keys as $v) {
            if (isset($arr[$v])) {
                $arr = $arr[$v];
            } else {
                return $default;
            }
        }
        return $arr;
    } else {
        return $default;
    }
}

/**
 * 获取协程ID
 * @return mixed
 */
function get_co_id()
{
    return \Swoole\Coroutine::getuid();
}

/**
 * 过滤xss
 * @param $str
 * @param null $allow_tags
 * @return string
 */
function filter_xss($str, $allow_tags = null)
{
    $str = strip_tags($str, $allow_tags);
    if ($allow_tags !== null) {
        while (true) {
            $l = strlen($str);
            $str = preg_replace('/(<[^>]+?)([\'\"\s]+on[a-z]+)([^<>]+>)/i', '$1$3', $str);
            $str = preg_replace('/(<[^>]+?)(javascript\:)([^<>]+>)/i', '$1$3', $str);
            if (strlen($str) == $l) {
                break;
            }
        }
    }
    return $str;
}

/**
 * 读取配置文件
 */
if (!function_exists('config')) {
    /**
     * 获取和设置配置参数
     * @param string|array $name 参数名
     * @param mixed $value 参数值
     * @return mixed
     */
    function config($name = '', $value = null)
    {
        if (is_null($value) && is_string($name)) {
            if ('.' == substr($name, -1)) {
                return Config::pull(substr($name, 0, -1));
            }

            return 0 === strpos($name, '?') ? Config::has(substr($name, 1)) : Config::get($name);
        } else {
            return Config::set($name, $value);
        }
    }
}

if (!function_exists('env')) {
    /**
     * 获取环境变量值
     * @access public
     * @param  string $name 环境变量名（支持二级 .号分割）
     * @param  string $default 默认值
     * @return mixed
     */
    function env($name = null, $default = null)
    {
        return Env::get($name, $default);
    }
}
if (!function_exists('cookie')) {
    /**
     * Cookie管理
     * @param string|array $name cookie名称，如果为数组表示进行cookie设置
     * @param mixed $value cookie值
     * @param mixed $option 参数
     * @return mixed
     */
    function cookie($name, $value = '', $option = null)
    {
        if (is_null($name)) {
            // 清除
            Cookie::clear($value);
        } elseif ('' === $value) {
            // 获取
            return 0 === strpos($name, '?') ? Cookie::has(substr($name, 1), $option) : Cookie::get($name);
        } elseif (is_null($value)) {
            // 删除
            return Cookie::delete($name);
        } else {
            // 设置
            return Cookie::set($name, $value, $option);
        }
    }
}

if (!function_exists('env')) {
    /**
     * 获取环境变量值
     * @access public
     * @param  string $name 环境变量名（支持二级 .号分割）
     * @param  string $default 默认值
     * @return mixed
     */
    function env($name = null, $default = null)
    {
        return Env::get($name, $default);
    }
}

if (!function_exists('session')) {
    /**
     * Session管理
     * @param string|array $name session名称，如果为数组表示进行session设置
     * @param mixed $value session值
     * @param string $prefix 前缀
     * @return mixed
     */
    function session($name, $value = '', $prefix = null)
    {
        if (is_array($name)) {
            // 初始化
            Session::init($name);
        } elseif (is_null($name)) {
            // 清除
            Session::clear($value);
        } elseif ('' === $value) {
            // 判断或获取
            return 0 === strpos($name, '?') ? Session::has(substr($name, 1), $prefix) : Session::get($name, $prefix);
        } elseif (is_null($value)) {
            // 删除
            return Session::delete($name, $prefix);
        } else {
            // 设置
            return Session::set($name, $value, $prefix);
        }
    }
}