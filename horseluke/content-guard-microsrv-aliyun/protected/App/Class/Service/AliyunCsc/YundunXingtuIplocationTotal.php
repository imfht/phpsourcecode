<?php

namespace Service\AliyunCsc;

use AlibabaSDK\Integrate\ServiceLocator;

/**
 * 批量IP归属地统计接口
 * @author HorseLuke
 *
 */
class YundunXingtuIplocationTotal{


    use DefaultServiceTrait;
    
    
    public function run($ips){
        $ips = $this->filterInputIps($ips);
        if(empty($ips)){
            return $this->setError("IP列表为空");
        }
        
        if(count($ips) > 10000){
            return $this->setError("IP太多");
        }
        
        $client = ServiceLocator::getInstance()->getService('TaobaoClient');
        $param = array();
        $param['ips'] = implode(',', $ips);
        
        $response = $client->send('alibaba.security.yundun.xingtu.iplocation.total', $param);
        
        $this->lastResponse = $response;
        
        if(!$response->isOk()){
            return $this->setError("请求发生错误，请联系管理员。". $response->getError());
        }
        
        $apires = $response->getResult();
        
        return $this->parseResult($apires);
        
        
    }
    
    protected function filterInputIps($ips){
        preg_match_all('/([0-9\.]+)(?:[, ]*)/', $ips, $m);
        if(empty($m)){
            return array();
        }
    
        $ips = array();
        foreach($m[1] as $ip){
            if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
                continue;
            }
            
            $ips[] = $ip;
        }
        
        return $ips;
        
    }
    
    
    protected function parseResult($apires){
        $result = array(
            'china_list' => array(),
            'global_list' => array(),
        );
        
        if(isset($apires['items']['china_list']['string'])){
            $result['china_list'] = $this->parseResultString($apires['items']['china_list']['string']);
        }
        
        if(isset($apires['items']['global_list']['string'])){
            $result['global_list'] = $this->parseResultString($apires['items']['global_list']['string']);
        }
        
        return $result;
    }
    
    
    protected function parseResultString($data){
        $result = array();
        foreach($data as $row){
            $row = explode(',', $row);
            $result[] = array('location' => $row[0], 'count' => $row[1]);
        }
        
        return $result;
    }
    
}