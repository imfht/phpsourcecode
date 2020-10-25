<?php
/**
 * RestClient.php
 * @author: liyongsheng
 * @email： liyongsheng@huimai365.com
 * @date: 14-8-25
 */
namespace framework\libraries;
use framework\core\Abnormal;

class RestException extends \Exception{}
/**
 * Class restClient
 * $client = new restClient();
 * $a = $client->sentGet('http://ip.taobao.com/service/getIpInfo.php?', array('ip'=>'124.127.184.99'));
 * print_r($client->getCurlInfo());
 * print_r($a);
 */
class RestClient
{
    /** @var null curl 实例 */
    private $_curl = null;
    /**
     * 基本设置
     * @var array
     */
    private $_options = array();
    public function __construct()
    {
        if(function_exists('curl_init')){
            throw new Abnormal('CURL module not available', 500);
        }
        $this->_resetOptions();
    }
    /**
     * 发送post 请求
     * @param $url
     * @param $data
     * @return bool|mixed
     */
    public function sendPost($url, $data)
    {
        if (empty($url)){
            return false;
        }
        $this->_create();
        curl_setopt($this->_curl, CURLOPT_POST, 1);
        curl_setopt($this->_curl, CURLOPT_URL, $url);
        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $data);
        return $this->_send();
    }

    /**
     * 发送 get 请求
     * @param $url
     * @param $data
     * @return bool|mixed
     */
    public function sendGet($url, $data)
    {
        if (empty($url)){
            return false;
        }
        $this->_create();
        $params = http_build_query($data);
        $url = rtrim($url, '?');
        if (strstr($url, '?')) {
            $url .= '&';
        } else {
            $url .= '?';
        }
        curl_setopt($this->_curl, CURLOPT_HTTPGET, 1);
        curl_setopt($this->_curl, CURLOPT_URL,$url.$params);
        return $this->_send();
    }

    /**
     * 发送 put 请求
     * @param $url
     * @param $data
     * @return bool|mixed
     */
    public function sendPut($url, $data)
    {
        if (empty($url)){
            return false;
        }
        $this->_create();
        curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->_curl, CURLOPT_URL,$url);
        return $this->_send();
    }

    /**
     * 发送 delete 请求
     * @param $url
     * @param $data
     * @return bool|mixed
     */
    public function sendDelete($url, $data)
    {
        if (empty($url)){
            return false;
        }
        $this->_create();
        curl_setopt($this->_curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($this->_curl, CURLOPT_HEADER, false);
        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->_curl, CURLOPT_URL,$url);
        return $this->_send();
    }
    /**
     * curl 选项设置
     * @param array $options
     * @return $this
     */
    public function setOpt(array $options)
    {
        $this->_options = array_merge($this->_options, $options);
        return $this;
    }

    /**
     * 获取curl info
     * @return mixed
     */
    public function getCurlInfo()
    {
        return curl_getinfo($this->_curl);
    }

    /**
     * 获取错误信息
     * @return array
     */
    public function getError()
    {
        return array(
            'code'=>curl_errno($this->_curl),
            'msg' => curl_error($this->_curl)
        );
    }

    /**
     * 初始化 curl
     */
    private function _create()
    {
        $this->_curl = curl_init();
        if (function_exists('curl_setopt_array')) {
            curl_setopt_array($this->_curl, $this->_options);
        } else {
            foreach ($this->_options as $option => $value) {
                curl_setopt($this->_curl, $option, $value);
            }
        }
    }

    /**
     * 执行curl
     * @throws RestException
     * @return mixed
     */
    private function _send()
    {
        $res = curl_exec($this->_curl);
        $curlInfo = curl_getinfo($this->_curl);
        if ($curlInfo['http_code']>400){
            throw new RestException(var_export($curlInfo, true), 500);
        }
        $this->_close();
        return $res;
    }
    /**
     * 关闭连接
     */
    private function _close()
    {
        curl_close($this->_curl);
        $this->_resetOptions();
        $this->_curl = null;
    }

    /**
     * 重置options
     */
    private function _resetOptions()
    {
        $this->_options = array(
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_RETURNTRANSFER => true
        );
        if(ini_get('open_basedir') == '' && strtolower(ini_get('safe_mode')) == 'off') {
            $this->_options[CURLOPT_FOLLOWLOCATION]=true;
        }
    }
    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->_close();
    }
}