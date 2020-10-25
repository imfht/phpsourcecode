<?php

namespace Http;

/**
 * http数据来源抽象类
 */
class AbstractModel {

    /**
     * 访问的host
     * 
     * @var string
     */
    protected $_host = '';

    /**
     * 发起HTTP请求
     * 
     * @param string $url
     * @param string $method
     * @param array $params
     * @param int $timeout
     * @param array $extParams 扩展的参数信息，可以是cookie之类
     * @return boolean
     */
    protected function _request($url, $method = "GET", $params = array(), $timeout = 30, $extParams = array()) {
        $url = $this->_host . $url;

        $paramString = http_build_query($params, '', '&');
        if (strtoupper($method) == "GET" && $params) {
            $url .= "?" . $paramString;
        }

        $ch = curl_init($url);

        if (strtoupper($method) == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramString);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        if (!empty($extParams["cookies"])) {
            curl_setopt($ch, CURLOPT_COOKIE, $this->analyzeCookie($extParams["cookies"]));
        }

        //检测是否是https访问
        if (strpos($url, 'https') === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        $result = curl_exec($ch);

        //请求失败的处理方法
        if (curl_errno($ch)) {
            \Our\Log::getInstance()->write('请求http接口失败，请求url:' . $url . '，Curl error: ' . curl_error($ch));
            return false;
        }
        curl_close($ch);

        return $result;
    }

    /**
     * 解析cookie数组，转换成字符串形式
     * 
     * @param array $cookies
     * @return string
     */
    public function analyzeCookie($cookies) {
        $cookie = '';
        foreach ($cookies as $key => $value) {
            $cookie = $key . '=' . $value . '; ';
        }

        return substr($cookie, 0, strlen($cookie) - 2);
    }

    /**
     * 获取主机地址
     * 
     * @return string
     */
    public function getHost() {
        return $this->_host;
    }

    /**
     * 设置主机地址
     * 
     * @return string
     */
    public function setHost($host) {
        $this->_host = $host;
    }

    /**
     * 禁止克隆
     */
    public function __clone() {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }

}
