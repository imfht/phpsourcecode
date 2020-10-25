<?php

/**
 * Request.php
 * @author: liyongsheng
 * @email： liyongsheng@huimai365.com
 * @date: 2015/6/10
*/

namespace framework\core;
use Templi;

/**
 * Class Request
 * @package framework\core
 * @property string $queryString
 * @property string $uri
 * @property string $rawBody
 * @property string $method
 * @property mixed $bodyParam
 */
class Request extends Object implements RequestInterface
{
    use Singleton;

    /** @var array 转换器容器 */
    private $_parsers = [
        'application/json'=> '\framework\core\Common::jsonDecode',
    ];
    /** @var  array http body */
    private $_bodyParams = [];

    /**
     * 获取post 值
     * @param $key string
     * @param mixed $default
     * @return mixed
     */
    public function post($key = null, $default = null)
    {
        return $this->getBodyParam($key = null, $default = null);
    }

    /**
     * 获取form 数据
     * @param null $key
     * @param null $default
     * @return null
     */
    public function form($key = null, $default = null)
    {
        if(is_null($key)){
            return $_POST;
        }else{
            return isset($_POST[$key]) ? $_POST[$key] : $default;
        }
    }
    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if(is_null($key)){
            return $_GET;
        }else{
            return isset($_GET[$key]) ? $_GET[$key] : $default;
        }
    }

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function put($key = null, $default = null)
    {
        return $this->getBodyParam($key = null, $default = null);
    }

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function delete($key = null, $default = null)
    {
        return $this->getBodyParam($key = null, $default = null);
    }
    /**
     * 获取 http Method
     * @return string
     */
    public function getMethod()
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } else {
            return isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        }
    }
    /**
     * 获取 http body 部分
     * @return mixed
     */
    public function getRawBody()
    {
        return file_get_contents('php://input');
    }

    /**
     * 获取 http body 部分
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getBodyParam($key = null, $default = null)
    {
        if(empty($this->_bodyParams)){
            $contentType = $this->getContentType();
            if(($pos = strpos($contentType, ';')) !== false){
                $contentType = substr($contentType, 0, $pos);
            }
            if(isset($this->_parsers[$contentType]) && is_callable($this->_parsers[$contentType])){
                $this->_bodyParams = call_user_func_array($this->_parsers[$contentType], [$this->getRawBody()]);
            }else{
                $this->_bodyParams = [];
                mb_parse_str($this->getRawBody(), $this->_bodyParams);
            }
        }
        if(is_null($key)){
            return $this->_bodyParams;
        }else{
            return isset($this->_bodyParams[$key]) ? $this->_bodyParams[$key] : $default;
        }
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * @return null
     */
    public function getContentType()
    {
        if (isset($_SERVER["CONTENT_TYPE"])) {
            return $_SERVER["CONTENT_TYPE"];
        } elseif (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
            return $_SERVER["HTTP_CONTENT_TYPE"];
        }

        return null;
    }

    /**
     * 设置数据转换器
     * @param array $parsers
     */
    public function setParsers(array $parsers)
    {
        $this->_parsers = array_merge($this->_parsers, $parsers);
    }

    /**
     * request uri
     * @return mixed
     */
    public function getUri()
    {
        return isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:null;
    }

    /**
     *
     * 获取应用相对 url
     * @return string
     */
    public function getQueryString()
    {
        return isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'';
    }
    /**
     * 获取客户端 ip
     *
     * @return string ip地址
     */
    public function getUserIp() {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }else{
            $ip = '';
        }
        return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    }

    /**
     * 获取userAgent
     * @return null|string
     */
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:null;
    }
    /**
     * 返回 url referrer
     * @return null
     */
    public function getReferrer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }
    /**
     * 是否为cli 方式运行
     * @return bool
     */
    public function isCli()
    {
        return PHP_SAPI ==='cli';
    }
}