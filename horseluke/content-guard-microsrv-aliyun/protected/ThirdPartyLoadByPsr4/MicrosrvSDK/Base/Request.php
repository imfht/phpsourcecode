<?php

namespace MicrosrvSDK\Base;

/**
 * 微服务API请求实体
 * @author Horse Luke
 *
 */
class Request{
    
    /**
     * (curl)关闭ssl证书verify
     * $protocol为https有效
     * @var string
     */
    protected $cfg_curlDisableSslVerify = true;
    
    /**
     * 等同于CURLOPT_TIMEOUT：The maximum number of seconds to allow cURL functions to execute.
     * @var int
     */
    protected $cfg_curlTimeout = 10;
    
    /**
     * 等同于CURLOPT_CONNECTTIMEOUT： The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
     * @var int
     */
    protected $cfg_curlConnectTimeout = 7;
    
    /**
     * 接口url
     * @var string
     */
    protected $cfg_gatewayUrl= "";
    
    /**
     * @var string
     */
    protected $cfg_appId = "";
    
    /**
     * @var string
     */
    protected $cfg_appSecret = "";
    
    /**
     * @var string
     */
    protected $cfg_version = "1.0";
    
	protected $_curlInit;
	
	protected $requestLoggerStack = array();
    
    /**
     * 初始化对象
     * @param array $config
     */
    public function __construct(array $config = null){
        if(!empty($config)){
            $this->setConfig($config);
        }
    }
    
    /**
     * 批量或单个设置配置
     * @param mixed $key 如果是数组，则批量设置，此时不用传value
     * @param mixed $value
     */
    public function setConfig($key, $value = null){
        if(is_array($key)){
            foreach ($key as $k => $v) {
                $k = 'cfg_' . $k;
                if (!property_exists($this, $k)) {
                    continue;
                }
                $this->{$k} = $v;
            }
            
        }else{
            $k = 'cfg_' . $key;
            if (property_exists($this, $k)) {
                $this->{$k} = $value;
            }
        }
        
    }
    
    /**
     * 获取配置
     * @param string $k
     * @return string
     */
    public function getConfig($k){
        $k = 'cfg_' . $k;
        return isset($this->{$k}) ? $this->{$k} : null;
    }
    
    /**
     * 设置一个requestLogger
     * @param string $name
     * @param CurlRequestLoggerInterface $logger
     */
    public function setRequestLogger($name, CurlRequestLoggerInterface $logger){
        $this->requestLoggerStack[$name] = $logger;
    }
    
    /**
     * 删除一个requestLogger
     * @param string $name
     */
    public function delRequestLogger($name){
        if(isset($this->requestLoggerStack[$name])){
            unset($this->requestLoggerStack[$name]);
        }
    }
    
    /**
     * 分发RequestLogger作记录
     * @param string $url
     * @param nill|string|array $finalBodyParam 请求的body体。
     *     null表示没有发送任何body体。
     *     string形式，表示以application/x-www-form-urlencoded组body。
     *     array形式，表示以multipart/form-data组body体。常见于文件上传。
     * @param string $requestMethod 请求方式
     * @param Response $response 结果
     */
    protected function dispatchRequestLogger($url, $finalBodyParam, $requestMethod, Response $response){
        foreach($this->requestLoggerStack as $logger){
            $logger->receiveSignalRequestLogger($url, $finalBodyParam, $requestMethod, $response);
        }
    }
    
    /**
     * 发送请求
     * @param string $actionName
     * @param string $param
     * @param string $requestMethod 请求方法，必须全大写
     * @param Response $response
     * @return Response $response
     */
    public function send($actionName, $param = null, $requestMethod = 'GET', Response $response = null){
        $url = $this->createUrl($actionName, $param, $requestMethod);
        $bodyParam = array();
        if('GET' != $requestMethod){
            $bodyParam = $param;
        }
        return $this->rawSend($url, $bodyParam, $requestMethod, $response);
    }
    
    /**
     * 原始发送请求
     * @param string $url 完整URL
     * @param string|array $bodyParam body请求体。$requestMethod为POST时有效
     * @param string $requestMethod 请求方法，必须全大写
     * @param Response $response
     * @return Response $response
     */
    public function rawSend($url, $bodyParam = null, $requestMethod = 'GET', Response $response = null){
        
        if(null === $response){
            $response = new Response();
        }
        
        if(null === $this->_curlInit){
            $this->_curlInit = curl_init();
        }
        
        $curlOpt = $this->getDefaultCurlOpt();
        
        $curlOpt[CURLOPT_URL] = $url;
        
        if($requestMethod == 'POST'){
            $curlOpt[CURLOPT_POST] = true;
        }
        $curlOpt[CURLOPT_CUSTOMREQUEST] = $requestMethod;
        
        if($requestMethod == 'POST' || $requestMethod == 'PUT'){
            if(is_array($bodyParam)){
                if(!$this->rawSendCheckHasFile($bodyParam)){
                    $bodyParam = http_build_query($bodyParam);
                }else{
                    $bodyParam = $this->rawSendBuildCleanUploadBody($bodyParam);
                }
            }
        
            if($bodyParam !== null && $bodyParam !== ""){
                $curlOpt[CURLOPT_POSTFIELDS] = $bodyParam;
            }else{
                $curlOpt[CURLOPT_POSTFIELDS] = "";
            }
        }
        
        curl_setopt_array($this->_curlInit, $curlOpt);
        
        $rawResult = curl_exec($this->_curlInit);
        $curlInfo = curl_getinfo($this->_curlInit);
        
        $curl_errno = curl_errno($this->_curlInit);
        if($curl_errno){
            $response->setError("CURL_ERROR", curl_error($this->_curlInit). '[ErrCode '. $curl_errno. ']');
        }else{
            $response->create($curlInfo['http_code'], $rawResult);
        }
        
        $response->setExtractInfo($curlInfo);
        
        if(!empty($this->requestLoggerStack)){
            $this->dispatchRequestLogger(
                $url,
                isset($curlOpt[CURLOPT_POSTFIELDS]) ? $curlOpt[CURLOPT_POSTFIELDS] : null,
                $requestMethod,
                $response
            );
        }
        
        return $response;
    }
    
    public function getDefaultCurlOpt(){
        $curlOpt = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => $this->cfg_curlTimeout,
            CURLOPT_CONNECTTIMEOUT => $this->cfg_curlConnectTimeout,
            CURLOPT_USERAGENT => (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'TDG_SRV no useragent'),
        );
    
        if ($this->cfg_curlDisableSslVerify) {
            $curlOpt[CURLOPT_SSL_VERIFYPEER] = false;
            $curlOpt[CURLOPT_SSL_VERIFYHOST] = 0;
        }
    
        if(defined('CURLOPT_SAFE_UPLOAD')){
            $curlOpt[CURLOPT_SAFE_UPLOAD] = true;
        }
        
        $curlOpt[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
        $curlOpt[CURLOPT_USERPWD] = $this->buildBasicAuthString();
    
        return $curlOpt;
    }
    
    /**
     * 判断请求body体是否有文件上传指令？
     * @param unknown $params
     * @return boolean
     */
    protected function rawSendCheckHasFile($params){
         
        foreach($params as $v){
            
            if($this->isUploadAtomCmd($v)){
                return true;
            }
            
        }
        
        return false;
    }
    
    /**
     * 如果判断为文件上传，则对数组body进行检查和净化
     * @param array $param
     * @return array
     */
    protected function rawSendBuildCleanUploadBody($param){
        foreach($param as $k => $v){
            
            if($this->isUploadAtomCmd($v)){
                continue;
            }
    
            //curl在CURLOPT_POSTFIELDS接收数组参数时，不支持field为数组
            if(is_array($v)){
                unset($param[$k]);
                continue;
            }
    
            if(is_string($v) && !empty($v) && $v{0} == '@'){
                unset($param[$k]);
                continue;
            }
    
        }
    
        return $param;
    }
    
    
    /**
     * 低版本PHP的curl_file_create函数兼容
     * @param string $filename
     * @param string $mimetype
     * @param string $postname
     * @return string
     */
    public function curl_file_create($filename, $mimetype = '', $postname = ''){
         
        if (!function_exists('curl_file_create')) {
            return new CURLFileCompat($filename, $mimetype, ($postname ? $postname : basename($filename)));
        }
        
        return curl_file_create($filename, $mimetype, $postname);
    }
    
    
    /**
     * 创建url
     * @param string $actionName
     * @param string $param
     * @param string $requestMethod 请求方法，必须全大写
     * @return string
     */
    public function createUrl($actionName, $param = null, $requestMethod = 'GET'){
        if(empty($param)){
            $param = array();
        }
        
        $systemParam = array();
        $systemParam['r'] = $actionName;
        $systemParam['m_appid'] = $this->cfg_appId;
        $systemParam['m_ver'] = $this->cfg_version;
        $systemParam['m_ip'] = $_SERVER['REMOTE_ADDR'];
        
        $finalParam = $systemParam;
        if($requestMethod != 'POST'){
            $finalParam = array_merge($finalParam, $param);
        }
        return $this->cfg_gatewayUrl. '?'. http_build_query($finalParam);
    }
    
    public function buildBasicAuthString(){
        $time = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
        $string = 'm_appid='. $this->cfg_appId. '&m_ip='. $_SERVER['REMOTE_ADDR'].  '&m_time='. $time;
        $pwd = md5($this->cfg_appSecret. $string);
        return strtoupper($time. ':'. $pwd);
    }
    
    /**
     * 是否是一个文件上传的原子指令？
     * @param mixed $v
     * @param bool
     */
    protected function isUploadAtomCmd($v){
    
        //CURLFileCompat为低于php 5.5的兼容
        if($v instanceof \CURLFile || $v instanceof CURLFileCompat){
            return true;
        }
    
        return false;
    }
    
    public function __destruct(){
        if ($this->_curlInit) {
		    curl_close($this->_curlInit);
        }
    }
    
}