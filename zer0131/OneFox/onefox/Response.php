<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc HTTP返回操作类
 */

namespace onefox;

class Response {

    private static $_cookieConfig = [
        'prefix' => '',
        'expire' => 0,
        'path' => '',
        'domain' => '',
        'secure' => false,
        'httponly' => true
    ];

    private static $_resData;
    private static $_resType;


    //设置返回类型
    public static function setResponseType($type) {
        self::$_resType = $type;
    }

    //获取返回类型
    public static function getResponseType() {
        return self::$_resType;
    }

    //设置输出数据
    public static function setResponseData($data) {
        self::$_resData = $data;
    }

    //获取返回数据
    public static function getResponseData() {
        return self::$_resData;
    }

    /**
     * 输出json数据
     * @param array $data
     */
    public static function json($data) {
        header('Content-Type:application/json; charset=utf-8');
        header('X-Powered-By: OneFox');
        header('SN: ' . REQUEST_ID);
        if (DEBUG) {
            $data['request_id'] = REQUEST_ID;
        }
        self::$_resData = $data;
        self::$_resType = 'application/json';
        exit(json_encode($data));
    }

    /**
     * 输出xml数据
     * @param array $data
     */
    public static function xml($data) {
        header('Content-Type:text/xml; charset=utf-8');
        header('X-Powered-By: OneFox');
        if (DEBUG) {
            $data['request_id'] = REQUEST_ID;
        }
        self::$_resData = $data;
        self::$_resType = 'text/xml';
        exit(C::xmlEncode($data));
    }

    /**
     * Cookie相关操作
     * @param string $name
     * @param string $value
     * @param string $opt
     * @return string|null
     */
    public static function cookie($name = '', $value = '', $opt = null) {
        //参数设置处理
        if (!is_null($opt)) {
            if (is_numeric($opt)) {
                $opt = ['expire' => $opt];//设置有效期
            }
            self::$_cookieConfig = array_merge(self::$_cookieConfig, $opt);
        }
        //清除所有Cookie
        if (is_null($name)) {
            $cookies = Request::cookies();
            if (!empty($cookies)) {
                foreach ($cookies as $key => $val) {
                    setcookie($key, '', time() - 3600, self::$_cookieConfig['path'], self::$_cookieConfig['domain'], self::$_cookieConfig['secure'], self::$_cookieConfig['httponly']);
                    Request::unsetParam($key, 'cookie');
                }
            }
            return null;
        } elseif ('' === $name) {
            return Request::cookies();//获取所有cookie
        }
        $cookieKey = self::$_cookieConfig['prefix'] . $name;
        if ('' === $value) {
            //获取指定的Cookie
            if (Request::cookie($cookieKey)) {
                return Request::cookie($cookieKey);
            }
            return null;
        } else {
            if (is_null($value)) {
                //删除指定Cookie
                setcookie($cookieKey, '', time() - 3600, self::$_cookieConfig['path'], self::$_cookieConfig['domain'], self::$_cookieConfig['secure'], self::$_cookieConfig['httponly']);
                Request::unsetParam($cookieKey, 'cookie');
            } else {
                //设置Cookie
                $expire = !empty(self::$_cookieConfig['expire']) ? time() + intval(self::$_cookieConfig['expire']) : 0;
                setcookie($cookieKey, $value, $expire, self::$_cookieConfig['path'], self::$_cookieConfig['domain'], self::$_cookieConfig['secure'], self::$_cookieConfig['httponly']);
            }
        }
        return null;
    }

    /**
     * 跳转
     * @param string $url
     * @param int $time
     * @param string $msg
     */
    public static function redirect($url, $time = 0, $msg = '') {
        //多行URL地址支持
        $url = str_replace([
            "\n",
            "\r"
        ], '', $url);
        if (empty($msg))
            $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
        if (!headers_sent()) {
            // redirect
            if (0 === $time) {
                header('Location: ' . $url);
            } else {
                header("refresh:{$time};url={$url}");
                echo($msg);
            }
            exit();
        } else {
            $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
            if ($time != 0)
                $str .= $msg;
            exit($str);
        }
    }
}

