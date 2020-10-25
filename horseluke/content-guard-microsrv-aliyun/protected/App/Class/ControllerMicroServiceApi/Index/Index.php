<?php

namespace ControllerMicroServiceApi\Index;

use SCH60\Kernel\BaseController;

/**
 * 微服务接口：首页
 * @author Administrator
 *
 */
class Index extends BaseController{
    
    /**
     * 微服务接口：首页，总是404
     */
    public function actionIndex(){
        $this->response->sendResponse(404);
        return $this->response->json(false, 1000, "NOT_FOUND");
    }
    
    /**
     * 微服务接口：测试连通性
     */
    public function actionPing(){
        $ping = $this->request->input($_GET, 'ping');
        if(strlen($ping) > 100){
            return $this->response->json(false, 1000, "ping太长");
        }
        
        return $this->response->json($ping);
        
    }
    
    
}