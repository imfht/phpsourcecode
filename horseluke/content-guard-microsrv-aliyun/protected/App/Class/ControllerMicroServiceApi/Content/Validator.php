<?php

namespace ControllerMicroServiceApi\Content;

use SCH60\Kernel\BaseController;
use Service\AliyunCsc\YundunSpamValidate;

/**
 * 微服务接口：内容验证器
 * @author Administrator
 *
 */
class Validator extends BaseController{
    
    /**
     * 检测内容是否有问题
     */
    public function actionCheckSpam(){
        $content = trim($this->request->input($_POST, 'content'));
        if(empty($content)){
            return $this->response->json(false, 1, "内容为空");
        }
        
        $spamValidateSrv = new YundunSpamValidate();
        
        $isOk = true;
        $error = "";
        if(!$spamValidateSrv->run($content)){
            $isOk = false;
            $error = $spamValidateSrv->getLastError();
        }
        
        $lastResponse = $spamValidateSrv->getLastResponse();
        if(null === $lastResponse || !$lastResponse->isOk()){
            return $this->response->json(false, 1, $error);
        }
        
        $result = array(
            'pass' => $isOk,
            'msg' => $error,
        );
        
        return $this->response->json($result);
        
    }
    
}