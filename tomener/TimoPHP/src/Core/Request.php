<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


use Timo\Exception\CoreException;

class Request
{
    protected static $instance;

    protected static $data;

    /**
     * 实例化类
     *
     * @return Request
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * 当前scriptFile的路径
     *
     * @return string
     * @throws CoreException
     */
    public function getScriptFilePath()
    {
        if (($scriptName = static::server('SCRIPT_FILENAME')) == null) {
            throw new CoreException('determine the entry script URL failed.');
        }

        return realpath(dirname($scriptName));
    }

    /**
     * 获取GET请求参数
     *
     * @param null $key
     * @param null $default
     * @param null $filter
     * @return mixed|null
     */
    public static function get($key = null, $default = null, $filter = null)
    {
        if (!$key) {
            return $_GET;
        }

        $value = isset($_GET[$key]) ? (!is_array($_GET[$key]) ? trim($_GET[$key]) : $_GET[$key]) : $default;

        $filter && $value = call_user_func($filter, $value);

        return $value;
    }

    public static function getInt($key, $default = 0)
    {
        return static::get($key, $default, 'intval');
    }

    public static function getString($key, $default = '')
    {
        return static::get($key, $default, 'trim');
    }

    /**
     * 获取POST请求参数
     *
     * @param null $key
     * @param null $default
     * @param null $filter
     * @return mixed|null|string
     */
    public static function post($key = null, $default = null, $filter = null)
    {
        if (!$key) {
            return $_POST;
        }
        if (!isset($_POST[$key])) {
            return $default;
        }

        $value = !is_array($_POST[$key]) ? trim($_POST[$key]) : $_POST[$key];

        $filter && $value = call_user_func($filter, $value);

        return $value;
    }

    public static function postInt($key, $default = 0)
    {
        return static::post($key, $default, 'intval');
    }

    public static function postString($key, $default = '')
    {
        return static::post($key, $default, 'trim');
    }

    public static function json($key = null, $default = '', $filter = null)
    {
        static::jsonInit();
        if (!$key) {
            return static::$data;
        }
        $value = !isset(static::$data[$key]) ? $default : static::$data[$key];
        $filter && $value = call_user_func($filter, $value);
        return $value;
    }

    public static function jsonInt($key, $default = 0)
    {
        return static::json($key, $default, 'intval');
    }

    public static function jsonString($key, $default = '')
    {
        return static::json($key, $default, 'trim');
    }

    protected static function jsonInit()
    {
        if (is_null(static::$data)) {
            static::$data = file_get_contents("php://input");
            static::$data = json_decode(static::$data, true);
            $err_code = json_last_error();
            if ($err_code == 0) {
                return;
            }
            if ($err_code == 4) {
                throw new \Exception('Request body is not in JSON format', $err_code);
            } else {
                throw new \Exception(json_last_error_msg(), $err_code);
            }
        }
    }

    /**
     * 取得$_SERVER全局变量的值
     *
     * @param null $key
     * @param null $default
     * @return null
     */
    public static function server($key = null, $default = null)
    {
        if (!$key) {
            return $_SERVER;
        }
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }

    /*
     * 取得$_ENV全局变量的值
     */
    public static function env($key = '', $default = null)
    {
        if (!$key) {
            return $_ENV;
        }
        return isset($_ENV[$key]) ? $_ENV[$key] : $default;
    }

    public static function method()
    {
        return static::server('REQUEST_METHOD');
    }

    /**
     * 是否GET请求
     *
     * @return bool
     */
    public static function isGet()
    {
        return static::server('REQUEST_METHOD') == 'GET';
    }

    /**
     * 是否POST请求
     *
     * @return bool
     */
    public static function isPost()
    {
        return static::server('REQUEST_METHOD') == 'POST';
    }

    /**
     * 是否AJAX请求
     *
     * @return bool
     */
    public static function isAjax()
    {
        return static::server('HTTP_X_REQUESTED_WITH') == 'xmlhttprequest';
    }

    /**
     * 获取客户端IP
     *
     * @return null|string
     */
    public static function getClientIP()
    {
        $ip = null;
        $remote_address = static::server('REMOTE_ADDR');

        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
            $ip = explode(',', $ip);
            $ip = trim(array_pop($ip));
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (!empty($remote_address) && strcasecmp($remote_address, 'unknown')) {
            $ip = $remote_address;
        }

        $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
        return $ip;
    }

    /**
     * 获取请求头信息
     *
     * @param string $name Content-Type
     * @param null $default 默认值
     * @return array|false|null
     */
    public static function getHeaders($name = '', $default = null)
    {
        $headers = [];
        if (!empty($name)) {
            $name = "HTTP_" . str_replace('-', '_', strtoupper($name));
            if (isset($_SERVER[$name])) {
                return $_SERVER[$name];
            } else {
                return $default;
            }
        }
        if (!function_exists('getallheaders')) {
            foreach ($_SERVER as $key => $value)
            {
                if (substr($key, 0, 5) == 'HTTP_')
                {
                    $headers[ucwords(strtolower(str_replace('_', '-', substr($key, 5))), '-')] = $value;
                }
            }
        } else {
            $headers = getallheaders();
        }
        return $headers;
    }
}
