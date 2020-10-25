<?php

/**
 * 框架一些不是很常用的方法封装
 * Date: 16-10-6
 * Time: 下午5:00
 * author :李华 yehong0000@163.com
 */
use \think\Session;
use \think\Cookie;
use \think\Cache;
class Helper
{
    public static function getRedis()
    {

    }
    /**
     * 缓存管理
     * @param mixed     $name 缓存名称，如果为数组表示进行缓存设置
     * @param mixed     $value 缓存值
     * @param mixed     $options 缓存参数
     * @param string    $tag 缓存标签
     * @return mixed
     */
    static function cache($name, $value = '', $options = null, $tag = null)
    {
        if (is_array($options)) {
            // 缓存操作的同时初始化
            Cache::connect($options);
        } elseif (is_array($name)) {
            // 缓存初始化
            return Cache::connect($name);
        }
        if ('' === $value) {
            // 获取缓存
            return Cache::get($name);
        } elseif (is_null($value)) {
            // 删除缓存
            return Cache::rm($name);
        } else {
            // 缓存数据
            if (is_array($options)) {
                $expire = isset($options['expire']) ? $options['expire'] : null; //修复查询缓存无法设置过期时间
            } else {
                $expire = is_numeric($options) ? $options : null; //默认快捷缓存设置过期时间
            }
            if (is_null($tag)) {
                return Cache::set($name, $value, $expire);
            } else {
                return Cache::tag($tag)->set($name, $value, $expire);
            }
        }
    }
    /**
     * Session管理
     * @param string|array  $name session名称，如果为数组表示进行session设置
     * @param mixed         $value session值
     * @param string        $prefix 前缀
     * @return mixed
     */
    static function session($name, $value = '', $prefix = null)
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
    /**
     * Cookie管理
     * @param string|array  $name cookie名称，如果为数组表示进行cookie设置
     * @param mixed         $value cookie值
     * @param mixed         $option 参数
     * @return mixed
     */
    static function cookie($name, $value = '', $option = null)
    {
        if (is_array($name)) {
            // 初始化
            Cookie::init($name);
        } elseif (is_null($name)) {
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