<?php

/**
 * Request
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;
use Madphp\Http\Util as Util;
use Madphp\Util\Validate as Validate;
use Madphp\Support\Str;

class Request extends Http\Request
{

    /**
     * 获取 IP Address
     *
     * @access   public
     * @return   string
     */
    public static function ipAddress()
    {

        if (self::$requestInstance->ipAddress !== FALSE) {
            return self::$requestInstance->ipAddress;
        }

        $proxyIps = Config::get('request', 'proxyIps', '');
        if (!empty($proxyIps)) {
            $proxyIps = explode(',', str_replace(' ', '', $proxyIps));
            foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header) {
                if (($spoof = self::server($header)) !== FALSE) {
                    if (strpos($spoof, ',') !== FALSE) {
                        $spoof = explode(',', $spoof, 2);
                        $spoof = $spoof[0];
                    }

                    if (!Validate::ip($spoof)) {
                        $spoof = FALSE;
                    } else {
                        break;
                    }
                }
            }

            self::$requestInstance->ipAddress = ($spoof !== FALSE && in_array($_SERVER['REMOTE_ADDR'], $proxyIps, TRUE)) ? $spoof : $_SERVER['REMOTE_ADDR'];
        } else {
            self::$requestInstance->ipAddress = $_SERVER['REMOTE_ADDR'];
        }

        if (!Validate::ip(self::$requestInstance->ipAddress)) {
            self::$requestInstance->ipAddress = '0.0.0.0';
        }

        return self::$requestInstance->ipAddress;
    }

    /**
     * 获取 User Agent
     *
     * @access   public
     * @return   string
     */
    public static function userAgent()
    {
        if (self::$requestInstance->userAgent !== FALSE) {
            return self::$requestInstance->userAgent;
        }

        self::$requestInstance->userAgent = (!isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];

        return self::$requestInstance->userAgent;
    }

    /**
     * 设置请求头信息到headers变量并返回
     *
     * @access  public
     * @param   是否清除xss字符
     * @return  array
     */
    public static function setHeaders($xssClean = FALSE)
    {
        // Look at Apache go!
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers['Content-Type'] = (isset($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : @getenv('CONTENT_TYPE');

            foreach ($_SERVER as $key => $val) {
                if (strncmp($key, 'HTTP_', 5) === 0) {
                    $headers[substr($key, 5)] = Util::fetch($_SERVER, $key, '', $xssClean);
                }
            }
        }

        foreach ($headers as $key => $val) {
            $key = str_replace('_', ' ', strtolower($key));
            $key = str_replace(' ', '-', ucwords($key));

            self::$requestInstance->headers[$key] = $val;
        }

        return self::$requestInstance->headers;
    }

    /**
     * 获取请求头信息
     *
     * @access   public
     * @param    string 键值
     * @param    string 默认值
     * @param    bool 是否清除xss字符
     * @return   string
     */
    public static function header($index = null, $default = '', $xssClean = FALSE)
    {
        if (empty(self::$requestInstance->headers)) {
            self::setHeaders();
        }
        if ($index === null) {
            $default = array();
        }
        return Util::fetch(self::$requestInstance->headers, $index, $default, $xssClean);
    }
    
   /**
    * 获取 $_SERVER 值
    *
    * @access   public
    * @param    string 键值
    * @param    string 默认值
    * @param    bool 是否清除xss字符
    * @return   mix
    */
    public static function server($index = null, $default = '', $xssClean = FALSE)
    {
        if ($index === null) {
            $default = array();
        }
        return Util::fetch($_SERVER, $index, $default, $xssClean);
    }

    /**
     * 获取请求方法
     * @access   public
     */
    public static function getMethod()
    {
        if (null === self::$requestInstance->method) {
            self::$requestInstance->method = strtoupper(self::server('REQUEST_METHOD'));

            if ('POST' === self::$requestInstance->method) {
                if ($method = self::server('X-HTTP-METHOD-OVERRIDE')) {
                    self::$requestInstance->method = strtoupper($method);
                } elseif (self::$requestInstance->httpMethodParameterOverride) {
                    self::$requestInstance->method = strtoupper(Input::post('_method', Input::get('_method', 'POST')));
                }
            }
        }

        return self::$requestInstance->method;
    }

    /**
     * 获取真实请求方法
     * @access   public
     */
    public static function getRealMethod()
    {
        return strtoupper(self::server('REQUEST_METHOD', 'GET'));
    }

    /**
     * 获取GET或POST请求数据
     *
     * @access   public
     * @param    string  键值
     * @param    string  未获取数据时的默认值
     * @param    bool    是否清除xss字符
     * @return   mix
     */
    public static function input($index = null, $default = '', $xssClean = FALSE)
    {
        if ($index === null) {
            $default = array();
        }
        if (!isset($_POST[$index])) {
            return Input::get($index, $default, $xssClean);
        } else {
            return Input::post($index, $default, $xssClean);
        }
    }

    /**
     * 是否 Ajax 请求
     *
     * @access  public
     * @return  boolean
     */
    public static function isAjax()
    {
        return (self::server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');
    }

    /**
     * 是否 cli 请求
     *
     * @access  public
     * @return  bool
     */
    public static function isCli()
    {
        return (php_sapi_name() === 'cli' OR defined('STDIN'));
    }

    /**
     * 是否 Json 数据请求
     *
     * @access   public
     * @return   bool
     */
    public static function isJson()
    {
        return Str::contains(self::header('CONTENT_TYPE'), '/json');
    }
}