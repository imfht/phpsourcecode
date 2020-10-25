<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc HTTP请求操作类
 */

namespace onefox;

use RuntimeException;

class Request {

    private static $_hasInit = false;
    private static $_getData = [];
    private static $_postData = [];
    private static $_cookieData = [];

    /**
     * 处理GET,POST,COOKIE的参数值
     */
    public static function deal() {
        if (!self::$_hasInit) {
            self::$_hasInit = true;
        } else {
            throw new RuntimeException('Request has initialized.');
        }
        self::$_getData = self::filterArray($_GET);
        self::$_postData = self::filterArray($_POST);
        self::$_cookieData = self::filterArray($_COOKIE);
        self::_resetRequestData();
    }

    /**
     * 返回某个GET请求值
     * @param string $key
     * @param string $default
     * @param string $type
     * @return string
     */
    public static function get($key, $default = null, $type = 'str') {
        self::_checkDeal();
        return self::filter($key, self::$_getData, $default, $type);
    }

    /**
     * 返回所有GET请求值
     * @return string
     */
    public static function gets() {
        self::_checkDeal();
        return self::$_getData;
    }

    public static function post($key, $default = null, $type = 'str') {
        self::_checkDeal();
        return self::filter($key, self::$_postData, $default, $type);
    }

    public static function posts() {
        self::_checkDeal();
        return self::$_postData;
    }

    public static function cookie($key, $default = null, $type = 'str') {
        self::_checkDeal();
        return self::filter($key, self::$_cookieData, $default, $type);
    }

    public static function cookies() {
        self::_checkDeal();
        return self::$_cookieData;
    }

    /**
     * 获取php://input流数据
     */
    public static function stream() {
        $content = file_get_contents('php://input');
        return $content;
    }

    /**
     * 设置单个请求参数
     * @param string $key
     * @param string $val
     * @param string $type (get/post)
     * @return boolean
     */
    public static function setParam($key, $val, $type = 'get') {
        self::_checkDeal();
        if ($type == 'get') {
            self::$_getData[$key] = $val;
        } else {
            self::$_postData[$key] = $val;
        }
        return true;
    }

    /**
     * 设置数组请求参数
     * @param array $data
     * @param string $type (get/post)
     * @return boolean
     */
    public static function setParams(array $data, $type = 'get') {
        self::_checkDeal();
        if (!is_array($data)) {
            return false;
        }

        if ($type == 'get') {
            self::$_getData = array_merge(self::$_getData, self::filterArray($data));
        } else {
            self::$_postData = array_merge(self::$_postData, self::filterArray($data));
        }
    }

    /**
     * 删除数组参数
     * @param string $key
     * @param string $type
     */
    public static function unsetParam($key, $type = 'get') {
        self::_checkDeal();
        if ('get' == $type) {
            unset(self::$_getData[$key]);
            unset($_GET[$key]);
        } elseif ('post' == $type) {
            unset(self::$_postData[$key]);
            unset($_POST[$key]);
        } elseif ('cookie' == $type) {
            unset(self::$_cookieData[$key]);
            unset($_COOKIE[$key]);
        }
    }

    /**
     * 请求方法
     * @return string
     */
    public static function method() {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * 是否是ajax请求
     * @return bool
     */
    public static function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
    }

    /**
     * 获取客户端IP地址
     * @param int $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param bool $adv 是否进行高级模式获取（有可能被伪装）
     * @return array|null|string
     */
    public static function ip($type = 0, $adv = false) {
        $type = $type ? 1 : 0;
        $ip = null;
        if ($ip !== null)
            return $ip;
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos)
                    unset($arr[$pos]);
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        //IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? [
            $ip,
            $long
        ] : [
            '0.0.0.0',
            0
        ];
        return $ip[$type];
    }

    /**
     * 安全过滤文本
     * @param string $txt
     * @return string
     */
    public static function filterText($txt) {
        $txt = trim($txt);
        if (XSS_MODE) {
            $txt = htmlspecialchars($txt);
        }
        if (ADDSLASHES_MODE) {
            $txt = addslashes($txt);
        }
        return $txt;
    }

    /**
     * 安全过滤数组
     * @param array|string $data
     * @return array
     */
    public static function filterArray($data) {
        if (!is_array($data)) {
            return self::filterText($data);
        }
        $res = [];
        foreach ($data as $key => $val) {
            $k = self::filterArray($key);
            $v = self::filterArray($val);
            $res[$k] = $v;
        }
        return $res;
    }

    /**
     * 过滤参数值类型
     * @param string $key
     * @param array $data
     * @param string $default
     * @param string $type
     * @return string
     */
    public static function filter($key, $data, $default, $type) {
        if (is_null($key) || !isset($data[$key])) {
            return $default;
        }
        switch ($type) {
            case 'int':
                return intval($data[$key]);
                break;
            case 'str':
            case 'string':
            case 'array':
                return $data[$key];
                break;
            default:
                return $default;
                break;
        }
    }

    /**
     * 检测初始化
     */
    private static function _checkDeal() {
        if (!self::$_hasInit) {
            self::deal();
        }
    }

    /**
     * 清除原始的请求数据
     */
    private static function _resetRequestData() {
        $_GET = null;
        $_POST = null;
        $_REQUEST = null;
        $_COOKIE = null;

        $_GET = self::$_getData;
        $_POST = self::$_postData;
        $_COOKIE = self::$_cookieData;

    }
}

