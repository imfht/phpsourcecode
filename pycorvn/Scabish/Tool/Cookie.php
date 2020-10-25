<?php
namespace Scabish\Tool;

/**
 * Scabish\Core\class
 * 对cookie进行存储、读取、更新、删除、清空操作
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2015-01-24
 */
class Cookie {
    
    /**
     * 设置cookie
     * @param string $name cookie名
     * @param string|array
     */
    public static function Set($name, $value, $expire = 3600, $path = '/', $domain = '') {
        $value = is_array($value) ? json_encode($value) : $value;
        setcookie(self::Rename($name), $value, time() + ($expire ? $expire : 3600), $path, $domain);
    }
    
    /**
     * 获取cookie值
     * @param string $name
     * @return string|array|false
     */
    public static function Get($name) {
        $name = self::Rename($name);
        if(isset($_COOKIE[$name]) && strlen($_COOKIE[$name]) != 0) {
            $tmp = json_decode($_COOKIE[$name]);
            if(is_array($tmp)) {
                return $tmp;
            }
            return $_COOKIE[$name];
        } else {
            return false;
        }
    }
    
    /**
     * 删除cookie
     * @param string $name
     */
    public static function Delete($name, $path = '/') {
        setcookie(self::Rename($name), '', time() - 3600*24*30, '/');
    }
    
    /**
     * 清空cookie
     */
    public static function Clear() {
        if(!empty($_COOKIE) && count($_COOKIE) != 0) {
            foreach($_COOKIE as $name=>$value) {
                setcookie($name, '', time() - 3600*24*30, '/');
            }
        }
    }
    
    /**
     * cookie重命名，防止与其他cookie值发生冲突覆盖情况
     * @param string $name
     */
    private static function Rename($name) {
        return substr(md5($name), 0, 8).'_'.$name;
    }
}