<?php

namespace MicrosrvSDK\Base;


class Response{

    
    protected $_code = -1;
    
    protected $_rawResult;
    
    protected $_result;
    
    protected $_error;
    
    protected $_isOk = false;
    
    protected $_errorDetail;
    
    protected $_extraInfo;
    
    protected $datatype = 'json';
    
    /**
     * 初始化对象
     * @param array $config
     */
    public function __construct(array $config = null){
        if(!empty($config)){
            $this->setConfig($config);
        }
    }
    
    public function setConfig(array $config){
        foreach($config as $k => $v){
            if($k{0} === '_'){
                continue;
            }
            $this->{$k} = $v;
        }
    }
    

    /**
     * 获取配置
     * @param string $k
     * @return string
     */
    public function getConfig($k){
        if($k{0} === '_'){
            return null;
        }
        return isset($this->{$k}) ? $this->{$k} : null;
    }
    
    public function create($code, $rawResult){
        $this->_code = $code;
        $this->_rawResult = $rawResult;
        
        if(empty($this->_rawResult)){
            return $this->setError('HTTP_BODY_EMPTY');
        }
        
        if(!empty($rawResult)){
            switch($this->datatype){
                case 'json':
                    $result = json_decode($rawResult, true);
                    
                    if(!is_array($result)){
                        return $this->setError('JSON_PARSE_ERROR');
                    }
                    
                    if($result['code'] != 0){
                        return $this->setError('API_RETURN_ERROR_CODE', $result['code']. ': '. $result['err']);
                    }
                    
                    $this->_result = $result['rst'];
                    break;
                default:
                    break;
            }
        }
        
        if($this->_code != 200){
            return $this->setError('HTTP_CODE_ERROR');
        }
        
        $this->_isOk = true;
        return true;
        
    }
    

    public function getCode(){
        return $this->_code;
    }

    public function getRawResult(){
        return $this->_rawResult;
    }
    
    public function getResult(){
        return $this->_result;
    }

    public function getError($withDetail = false){
        if(!$withDetail){
            return $this->_error;
        }
        return array('error' => $this->_error, 'errorDetail' => $this->_errorDetail);
    }
    
    public function isOk(){
        return $this->_isOk;
    }
    
    public function setError($error, $errorDetail = null){
        $this->_error = $error;
        if(!empty($errorDetail)){
            $this->_errorDetail = $errorDetail;
        }
        return false;
    }
    
    public function setExtractInfo($info){
        $this->_extraInfo = $info;
    }
    
    public function getExtractInfo(){
        return $this->_extraInfo;
    }
    
}