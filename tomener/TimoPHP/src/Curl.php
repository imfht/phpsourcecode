<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo;


class Curl
{
    /**
     * 浏览器的AGENT信息
     *
     * @var string
     */
    protected static $_userAgent = null;

    /**
     * cookie的存贮文件路径
     *
     * @var string
     */
    protected static $_cookieFilePath = null;

    /**
     * 是否curl get获取页面信息时支持cookie存贮
     *
     * @var boolean
     */
    protected static $_cookieSupport = false;

    /**
     * 设置浏览器的AGENT信息
     *
     * @param string $userAgent 浏览器的AGENT信息
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        static::$_userAgent = $userAgent;
        return $this;
    }

    /**
     * 设置cookie的存贮文件路径
     *
     * @param string $filePath 存储cookie的文件路径
     * @return $this
     */
    public function setCookieFile($filePath)
    {
        static::$_cookieSupport = true;
        static::$_cookieFilePath = $filePath;

        return $this;
    }

    /**
     * 设置cookie功能是否开启
     *
     * @access public
     *
     * @param boolean $isOn 是否开启
     *
     * @return object $this
     */
    public function setCookieStatus($isOn = true)
    {
        static::$_cookieSupport = $isOn;
        return $this;
    }

    /**
     * 用CURL模拟获取网页页面内容
     *
     * @access public
     *
     * @param string $url 所要获取内容的网址
     * @param array $data 所要提交的数据
     * @param array $header 请求头
     * @param int $expire 时间限制
     * @param string $proxy 代理设置
     * @throws \Exception
     *
     * @return string
     */
    public static function get($url, $data = [], $header = [], $expire = 30, $proxy = null)
    {
        //参数分析
        if (!$url) {
            return false;
        }

        //分析是否开启SSL加密
        $ssl = strtolower(substr($url, 0, 8)) == 'https://' ? true : false;

        //读取网址内容
        $ch = curl_init();

        //设置代理
        if (!$proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        //分析网址中的参数
        if ($data) {
            $paramUrl = http_build_query($data, '', '&');
            $extStr = (strpos($url, '?') !== false) ? '&' : '?';
            $url = $url . (($paramUrl) ? $extStr . $paramUrl : '');
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        if ($ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if (static::$_cookieSupport === true) {
            $cookieFile = static::_parseCookieFile();
            //cookie设置
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        }

        //设置浏览器
        if (static::$_userAgent || isset($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, (!static::$_userAgent) ? $_SERVER['HTTP_USER_AGENT'] : static::$_userAgent);
            curl_setopt($ch, CURLOPT_HEADER, 0);
        }

        //设置头部
        if (!empty($header)) {
            $headers = [];
            foreach ($header as $key => $val) {
                array_push($headers, "$key: $val");
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        //使用自动跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $expire);

        $content = curl_exec($ch);
        if (!$content) {
            $err_no = curl_errno($ch);
            if($err_no != 0) {
                $err = curl_error($ch);
                curl_close($ch);
                throw new \Exception($err, $err_no);
            }
        }
        curl_close($ch);

        return $content;
    }

    /**
     * 获取cookie存贮文件的路径
     *
     * @return string
     */
    protected static function _parseCookieFile()
    {
        //分析cookie数据存贮文件
        if (static::$_cookieFilePath) {
            return static::$_cookieFilePath;
        }

        return CACHE_PATH . '/temp/' . md5('timophp_curl_cookie') . '.txt';
    }

    /**
     * 用CURL模拟提交数据
     *
     * @param string $url post所要提交的网址
     * @param array $data 所要提交的数据
     * @param array $header 请求头
     * @param int $expire 所用的时间限制
     * @param string $proxy 代理设置
     * @throws \Exception
     *
     * @return string
     */
    public static function post($url, $data = [], $header = [], $expire = 30, $proxy = null)
    {
        //参数分析
        if (!$url) {
            return false;
        }

        //分析是否开启SSL加密
        $ssl = strtolower(substr($url, 0, 8)) == 'https://' ? true : false;

        //读取网址内容
        $ch = curl_init();

        //设置代理
        if (!$proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        if ($ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if (static::$_cookieSupport === true) {
            $cookieFile = static::_parseCookieFile();
            //cookie设置
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        }

        //设置浏览器
        if (static::$_userAgent || isset($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, (!static::$_userAgent) ? $_SERVER['HTTP_USER_AGENT'] : static::$_userAgent);
            curl_setopt($ch, CURLOPT_HEADER, 0);
        }

        //设置头部
        if (!empty($header)) {
            $headers = [];
            foreach ($header as $key => $val) {
                array_push($headers, "$key: $val");
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        //发送一个常规的Post请求
        curl_setopt($ch, CURLOPT_POST, true);
        //Post提交的数据包
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        //使用自动跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $expire);

        $content = curl_exec($ch);
        if (!$content) {
            $err_no = curl_errno($ch);
            if($err_no != 0) {
                $err = curl_error($ch);
                curl_close($ch);
                throw new \Exception($err, $err_no);
            }
        }
        curl_close($ch);

        return $content;
    }

    /**
     * 发送json POST请求
     *
     * @param $method
     * @param $url
     * @param $data
     * @param int $expire
     * @param null $proxy
     * @return mixed
     */
    public static function jsonPost($url, $data, $expire = 30, $proxy = null)
    {
        return call_user_func_array([Curl::class, 'post'], [$url, json_encode($data), ['Content-Type: application/json'], $expire, $proxy]);
    }
}
