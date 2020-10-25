<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap;

/**
 * HttpCookie class file
 * HTTPCookie管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: HttpCookie.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
class HttpCookie
{
    /**
     * 获取所有的Cookie
     * @return array
     */
    public static function toArray()
    {
        return $_COOKIE;
    }

    /**
     * 获取Cookie数据量
     * @return integer
     */
    public static function count()
    {
        return count($_COOKIE);
    }

    /**
     * 获取Cookie中所有的键
     * @return array
     */
    public static function getNames()
    {
        return array_keys($_COOKIE);
    }

    /**
     * 通过Cookie名获取值，如果都找不到，返回默认值
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public static function get($name, $defaultValue = null)
    {
        return self::has($name) ? $_COOKIE[$name] : $defaultValue;
    }

    /**
     * 添加Cookie，如果Cookie名已经存在，且不更新服务器路径和域名，则替换老值
     * @param string $name      Cookie名
     * @param string $value     Cookie值
     * @param integer $expiry   Cookie有效期
     * @param string $path      Cookie的有效服务器路径
     * @param string $domain    Cookie的有效域名
     * @param boolean $secure   FALSE：HTTP和HTTPS协议都可传输；TRUE：只通过加密的HTTPS协议传输
     * @param boolean $httponly TRUE：只能通过HTTP协议访问；FALSE：HTTP协议和脚本语言都可访问，容易造成XSS攻击
     * @return boolean
     */
    public static function add($name, $value, $expiry = 0, $path = '', $domain = '', $secure = false, $httponly = false)
    {
        return setcookie($name, $value, $expiry, $path, $domain, $secure, $httponly);
    }

    /**
     * 移除Cookie
     * @param string $name   Cookie名
     * @param string $path   Cookie的有效服务器路径
     * @param string $domain Cookie的有效域名
     * @return boolean
     */
    public static function remove($name, $path = '/', $domain = '')
    {
        return setcookie($name, false, time() - 31536000, $path, $domain); // 过期1年
    }

    /**
     * 判断Cookie名在Cookie中是否存在
     * @param string $name
     * @return boolean
     */
    public static function has($name)
    {
        return isset($_COOKIE[$name]);
    }
}
