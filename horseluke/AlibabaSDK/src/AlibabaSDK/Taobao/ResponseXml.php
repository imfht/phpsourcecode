<?php

namespace AlibabaSDK\Taobao;

use AlibabaSDK\Base\Response;

/**
 * 以xml方式解析结果，并转化为数组
 * @author Horse Luke
 *
 */
class ResponseXml extends Response
{
    
    /**
     * 以xml方式解析结果，并转化为数组
     * @return boolean
     */
    protected function parseResult(){
        $result = simplexml_load_string($this->rawResult);
        if (false === $result) {
            return $this->setError('PARSE_ERROR_RESPONSE_XML');
        }
        
        $this->result = (array)$result;
        
        $detectError = $this->parseResultErrorByArray();
        if(!empty($detectError)){
            return $this->setError('API_RETURN_ERROR_CODE', $detectError);
        }
        
        return true;
        
    }
    
    /**
     * 解析结果数组是否有错误
     * 仅供特定方法parseResult_*使用
     * @return string
     */
    protected function parseResultErrorByArray(){
        $errorMsg = '';
        if(!empty($this->result['code'])){
            $errorMsg = $this->result['code']. ':'. $this->result['msg'];
            if(!empty($this->result['sub_code'])){
                $errorMsg .= ':'. $this->result['sub_code'];
            }
            if(!empty($this->result['sub_msg'])){
                $errorMsg .= ':'. $this->result['sub_msg'];
            }
        }
    
        return $errorMsg;
    }
    
}