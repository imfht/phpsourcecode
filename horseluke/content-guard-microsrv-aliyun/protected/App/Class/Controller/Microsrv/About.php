<?php

namespace Controller\Microsrv;

use SCH60\Kernel\BaseController;

class About extends BaseController{
    
    use DemoForGetMicrosrvRequestControllerTrait;
    
    protected $layout = "layout_default";
    
    public function actionPing(){
        $request = $this->getMicrosrvRequest();
        $response = $request->send('index/index/ping', array('ping' => 1));
        
        $viewData = array();
        $viewData['title'] = "接入检测 - 内容安全微服务";
        $viewData['parentTitle'] = "系统管理";
        $viewData['url'] = $request->getConfig('gatewayUrl');
		$viewData['isOk'] = $response->isOk();
		$viewData['error'] = $response->getError(true);
		
		return $this->render(null, $viewData);
    }
    
}