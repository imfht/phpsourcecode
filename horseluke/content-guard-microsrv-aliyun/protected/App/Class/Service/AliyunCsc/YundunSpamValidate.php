<?php

namespace Service\AliyunCsc;

use AlibabaSDK\Integrate\ServiceLocator;

/**
 * 阿里云安全内容检测器Service
 * @author HorseLuke
 *
 */
class YundunSpamValidate{
    

    use DefaultServiceTrait;
    
    /**
     * 检查是否有spam？
     * @param string $content
     * @param int $uid
     * @return bool
     */
    public function run($content, $uid = null){
        
        if(empty($content)){
            return $this->setError('内容为空。');
        }
        
        $client = ServiceLocator::getInstance()->getService('TaobaoClient');
        
        $param = array();
        $param['content'] = $content;
        if(!empty($uid)){
            $param['user_id'] = $uid;
        }

        //configs参数部分为定制需求，如有需要，请和阿里云的工作人员联系
        $param['configs'] = array();
        $param['configs'][] = array(
            'id' => 'forbidden_words_rule',
            'params' => array()
        );
        $param['configs'][] = array(
            'id' => 'spam_text_rule',
            'params' => array()
        );
        $param['configs'] = json_encode($param['configs']);
        //configs参数定制完毕
        
        $response = $client->send('alibaba.security.yundun.spam.validate', $param);
        
        $this->lastResponse = $response;
        
        if(!$response->isOk()){
            return $this->setError("检测请求发生错误，请联系管理员。". $response->getError());
        }
        
        $apires = $response->getResult();
        if($apires['result']['code'] === null || $apires['result']['code'] != 0){
            return $this->setError("无法通过审核！原因：". $apires['result']['msg']);
        }
        
        return true;
        
    }
    
}
