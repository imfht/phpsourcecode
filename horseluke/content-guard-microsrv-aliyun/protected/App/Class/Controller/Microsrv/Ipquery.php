<?php

namespace Controller\Microsrv;

use SCH60\Kernel\BaseController;

class Ipquery extends BaseController{
    
    use DemoForGetMicrosrvRequestControllerTrait;
    
    protected $layout = "layout_default";
    
    public function actionAttackHistory(){
        $ip = $this->request->input($_GET, 'ip');
        
        $isOk = false;
        $result = $error = null;
        if(!empty($ip) && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
            $request = $this->getMicrosrvRequest();
            $response = $request->send('ip/query/attackHistory', array('ip' => $ip));
            $result['isOk'] = $response->isOk();
            if($response->isOk()){
                $isOk = true;
                $result = $response->getResult();
            }else{
                $isOk = false;
                $error = $response->getError(true);
            }
        }
        
        $viewData = array();
        $viewData['title'] = "IP攻击历史记录查询 - 内容安全微服务";
        $viewData['parentTitle'] = "系统管理";
        
        $viewData['ip'] = $ip;
        $viewData['isOk'] = $isOk;
        $viewData['result'] = $result;
        $viewData['error'] = $error;
        
        return $this->render(null, $viewData);
    }
    
}