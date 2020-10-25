<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\request;

use nb\Pool;

/**
 * Native
 *
 * @package nb\request
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 */
class Php extends Driver {

    public function __construct() {
        $this->tmp = [
            'input'   => file_get_contents('php://input'),
            'get'     => &$_GET,
            'post'    => &$_POST,
            'request' => &$_REQUEST,
            'files'   => &$_FILES,
            'cookie'  => &$_COOKIE,
            'server'  => &$_SERVER
        ];
    }

    /**
     * 是否是Post请求
     * @return bool
     */
    protected function _isPost(){
        $method = $this->method;
        if($method === 'POST') {
            return true;
        }
        return false;
    }

    /**
     * 是否是Get请求
     * @return bool
     */
    protected function _isGet(){
        $method = $this->method;
        if($method === 'GET') {
            return true;
        }
        return false;
    }

    /**
     * 是否是Ajax请求
     * @return bool
     */
    protected function _isAjax(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH']) {
            return true;
        }
        return false;
    }

    protected function _isPjax() {
        if(isset($_SERVER['HTTP_X_PJAX'])) {
            return true;
        }
        return false;
    }

    protected function _requestTime() {
        return $this->server['REQUEST_TIME'];
    }

    protected function _requestTimeFloat() {
        return $this->server['REQUEST_TIME_FLOAT'];
    }

    protected function _method(){
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * isMobile
     *
     * @static
     * @access public
     * @return boolean
     */
    protected function _isMobile() {
        $userAgent = $this->_agent();
        return preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $userAgent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4));
    }

    /**
     * 设置或获取当前包含协议的域名
     * @access public
     * @param string $domain 域名
     * @return string
     */
    protected function _domain() {
        return $this->scheme . '://' . $this->host;
    }

    /**
     * 获取客户端
     *
     * @access public
     * @return string
     */
    protected function _agent() {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    protected function _ip() {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim(current($arr));
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * 当前请求的host
     * @access public
     * @return string
     */
    public function _host() {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * 设置来源页
     *
     * @access public
     * @param string $referer 客户端字符串
     * @return void
     */
    public function _referer() {
        return $_SERVER['HTTP_REFERER'];
    }

    /**
     * 当前URL地址中的scheme参数
     * @access public
     * @return string
     */
    public function _scheme() {
        return $this->isSsl ? 'https' : 'http';
    }

    /**
     * 当前是否ssl
     * @access public
     * @return bool
     */
    public function _isSsl() {
        $server = $_SERVER;
        if (isset($server['HTTPS']) && ('1' == $server['HTTPS'] || 'on' == strtolower($server['HTTPS']))) {
            return true;
        }
        elseif (isset($server['REQUEST_SCHEME']) && 'https' == $server['REQUEST_SCHEME']) {
            return true;
        }
        elseif (isset($server['SERVER_PORT']) && ('443' == $server['SERVER_PORT'])) {
            return true;
        }
        elseif (isset($server['HTTP_X_FORWARDED_PROTO']) && 'https' == $server['HTTP_X_FORWARDED_PROTO']) {
            return true;
        }
        return false;
    }

    /**
     * 获取当前请求url
     *
     * @access public
     * @return string
     */
    public function _url() {
        return $this->url = $this->domain . $this->uri;
    }

    /**
     * 获取当前请求url，不含"？"及参数
     *
     * @access public
     * @return string
     */
    public function _addr() {
        return $this->url = $this->domain . explode('?',$this->uri)[0];
    }

    /**
     * 获取请求地址
     *
     * @access public
     * @return string
     */
    public function _uri() {
        //处理requestUri
        $requestUri = '/';

        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        }
        elseif (
            // IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
            isset($_SERVER['IIS_WasUrlRewritten'])
            && $_SERVER['IIS_WasUrlRewritten'] == '1'
            && isset($_SERVER['UNENCODED_URL'])
            && $_SERVER['UNENCODED_URL'] != ''
        ) {
            $requestUri = $_SERVER['UNENCODED_URL'];
        }
        elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            $parts = @parse_url($requestUri);

            if (isset($_SERVER['HTTP_HOST']) && strstr($requestUri, $_SERVER['HTTP_HOST'])) {
                if (false !== $parts) {
                    $requestUri = (empty($parts['path']) ? '' : $parts['path'])
                        . ((empty($parts['query'])) ? '' : '?' . $parts['query']);
                }
            }
            elseif (!empty($_SERVER['QUERY_STRING']) && empty($parts['query'])) {
                // fix query missing
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        }

        return $requestUri;
    }

    /**
     * 获取当前pathinfo
     *
     * @access public
     * @param string $inputEncoding 输入编码
     * @param string $outputEncoding 输出编码
     * @return string
     */
    public function _pathinfo() {

        if(isset($this->server['PATH_INFO'])) {
            return $this->server['PATH_INFO'];
        }

        return str_replace(
            '/index.php',
            '',
            explode('?',$this->uri)[0]
        );


        $inputEncoding = NULL;
        $outputEncoding = NULL;
        //参考Zend Framework对pahtinfo的处理, 更好的兼容性
        $pathInfo = NULL;

        //处理requestUri
        $requestUri   = $this->uri;
        $finalBaseUrl = $this->baseurl;

        // Remove the query string from REQUEST_URI
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        if ((NULL !== $finalBaseUrl) && (false === ($pathInfo = substr($requestUri, strlen($finalBaseUrl))))  ) {
            // If substr() returns false then PATH_INFO is set to an empty string
            $pathInfo = '/';
        }
        elseif (NULL === $finalBaseUrl) {
            $pathInfo = $requestUri;
        }

        if ($pathInfo) {
            //针对iis的utf8编码做强制转换
            //参考http://docs.moodle.org/ja/%E5%A4%9A%E8%A8%80%E8%AA%9E%E5%AF%BE%E5%BF%9C%EF%BC%9A%E3%82%B5%E3%83%BC%E3%83%90%E3%81%AE%E8%A8%AD%E5%AE%9A
            if (!empty($inputEncoding) && !empty($outputEncoding) && (stripos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false || stripos($_SERVER['SERVER_SOFTWARE'], 'ExpressionDevServer') !== false) ) {
                if (function_exists('mb_convert_encoding')) {
                    $pathInfo = mb_convert_encoding($pathInfo, $outputEncoding, $inputEncoding);
                }
                else if (function_exists('iconv')) {
                    $pathInfo = iconv($inputEncoding, $outputEncoding, $pathInfo);
                }
            }
        }
        else {
            $pathInfo = '/';
        }

        return '/' . ltrim(urldecode($pathInfo), '/');
    }

    /**
     * 获取url的扩展名
     * @return null
     */
    protected function _ext() {
        $url = $this->uri;
        $urlinfo =  parse_url($url);
        $file = basename($urlinfo['path']);
        if(strpos($file,'.') !== false) {
            $ext = explode('.',$file);
            return $ext[count($ext)-1];
        }
        return null;
    }


    /**
     * 获取表单数据，返回一个结果数组
     * @param string $method
     * @param null $args
     * @return array
     */
    public function form($method='request',array $args=null) {
        $method = $method === 'auto'?strtolower($this->method()):$method;
        $input = [];
        switch ($method) {
            case 'request':
                $input = $this->request;
                break;
            case 'post':
                $input = $this->post;
                break;
            case 'get':
                $input = $this->get;
                break;
            case 'request':
                $input = $this->request;
                break;
            case 'input':
                $input = $this->input;
                break;
            case 'put':
                parse_str($this->input, $input);
                break;
            case 'files':
                $input = $this->files;
                break;
            case 'server':
                $input = $this->server;
                break;
        }
        if($args) {
            $_input = [];
            foreach ($args as $arg) {
                $_input[$arg] = isset($input[$arg])?$input[$arg]:null;
            }
            $input = $_input;
        }
        return $input;
    }

    /**
     * 获取表单参数对应的值
     * 如果获取多个，则以值数组的形式返回
     *
     * @param $arg
     * @param array ...$args
     * @return array|mixed|null
     */
    public function input($arg,...$args){
        /** $args != null */
        if($args) {
            if(is_array($args[0])) {
                //$this->input('get',['name','pass']);
                $args = $args[0];
                $method = $arg;
            }
            else {
                //$this->input('name','pass');
                array_unshift($args,$arg);
                $method = 'request';
            }
        }
        else {
            /** $args == null */
            //$this->input('name');
            //$this->input(['name','pass']);
            $args = [$arg];
            $method = 'request';
        }

        $input = $this->form($method,$args);

        if(is_array($input) === false) {
            return null;
        }

        if(count($input) == 1) {
            return current($input);
        }

        return array_values($input);
    }

}