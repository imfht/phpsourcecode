<?php

namespace Service\AliyunCsc;

use AlibabaSDK\Integrate\ServiceLocator;

/**
 * 恶意ip事件接口
 * @author HorseLuke
 *
 */
class YundunXingtuIpattacksQuery{


    use DefaultServiceTrait;
    
    public function run($ip){
        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
            return $this->setError("IP不正确");
        }
        
        $client = ServiceLocator::getInstance()->getService('TaobaoClient');
        $param = array();
        $param['ip'] = $ip;
        
        $response = $client->send('alibaba.security.yundun.xingtu.ipattacks.query', $param);
        
        $this->lastResponse = $response;
        
        if(!$response->isOk()){
            return $this->setError("请求发生错误，请联系管理员。". $response->getError());
        }
        
        return $response->getResult();
        
    }
    
}