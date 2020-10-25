<?php

/**
 * Cookie
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;
use Madphp\Http\Util as Util;

class Cookie extends Http\Request
{
    /**
     * 获取COOKIE
     *
     * @access   public
     * @param    string
     * @param    string
     * @param    bool
     * @return   mixed
     */
    public static function get($index = null, $default = '', $prefix = '', $xssClean = FALSE)
    {
        $data = $_COOKIE;
        
        if ($prefix == '' AND Config::get('request', 'cookiePrefix', '') != '') {
            $prefix = Config::get('request', 'cookiePrefix');
        }
        
        if($prefix == '') {
            if ($index === null) {
                $name = null;
                $default = array();
            } else {
                $name = $prefix.$index;
            }
        } else {
            if ($index === null) {
                foreach ($_COOKIE as $key => $v) {
                    if (!preg_match("#^".$prefix."#", $key)) {
                        unset($data[$key]);
                    }
                }
                $name = null;
                $default = array();
            } else {
                $name = $prefix.$index;
            }
        }

        return Util::fetch($data, $name, $default, $xssClean);
    }
    
    /**
     * 设置 cookie
     *
     * @access   public
     * @param    mixed   名  可以是数组
     * @param    string  值
     * @param    string  过期时间
     * @param    string  域  一般设置为:  .yourdomain.com
     * @param    string  路径
     * @param    string  前缀
     * @param    bool    是否通过安全的 HTTPS 连接来传输 cookie
     * @return   void
     */
    public static function set($name = '', $value = '', $expire = 0, $domain = '', $path = '/', $prefix = '', $secure = FALSE)
    {
        if (is_array($name)) {
            foreach (array('value', 'expire', 'domain', 'path', 'prefix', 'secure', 'name') as $item) {
                if (isset($name[$item])) {
                    $$item = $name[$item];
                }
            }
        }

        if ($prefix == '' AND Config::get('request', 'cookiePrefix', '') != '') {
            $prefix = Config::get('request', 'cookiePrefix');
        }

        if ($domain == '' AND Config::get('request', 'cookieDomain', '') != '') {
            $domain = Config::get('request', 'cookieDomain');
        }

        if ($path == '/' AND Config::get('request', 'cookiePath', '/') != '/') {
            $path = Config::get('request', 'cookiePath');
        }

        if ($secure == FALSE AND Config::get('request', 'cookieSecure', FALSE) != FALSE) {
            $secure = Config::get('request', 'cookieSecure');
        }

        // 过期时间设置为非数字或数字字符串，删除 cookie
        if (!is_numeric($expire)) {
            $expire = time() - 86500;
        } else {
            // 小于等于 0 设置会话 cookie
            $expire = ($expire > 0) ? time() + $expire : 0;
        }

        setcookie($prefix.$name, $value, $expire, $path, $domain, $secure);
    }

    /**
     * 删除 cookie
     *
     * @access   public
     * @param    mixed   名  可以是数组
     * @param    string  域  一般设置为:  .yourdomain.com
     * @param    string  路径
     * @param    string  前缀
     * @return   void
     */
    public static function delete($name = '', $domain = '', $path = '/', $prefix = '')
    {
        self::set($name, '', '', $domain, $path, $prefix);
    }
}