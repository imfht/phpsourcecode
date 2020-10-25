<?php

namespace ControllerMicroServiceApi\Ip;

use SCH60\Kernel\BaseController;
use Service\AliyunCsc\YundunXingtuIplocationTotal;
use Service\AliyunCsc\YundunXingtuIpattacksQuery;

/**
 * IP信息查询
 * @author Administrator
 *
 */
class Query extends BaseController{
    
    /**
     * 获取一批ip的地理统计
     */
    public function actionGeoStat(){
        $ips = $this->request->input($_POST, 'ips');
        
        $service = new YundunXingtuIplocationTotal();
        
        $res = $service->run($ips);
        
        if(false === $res){
            $this->response->json(false, 1, $service->getLastError());
        }
        
        $this->response->json($res);
        
    }
    
    /**
     * 获取一个ip的恶意事件统计
     */
    public function actionAttackHistory(){
        $ip = $this->request->input($_GET, 'ip');
        
        $service = new YundunXingtuIpattacksQuery();
        
        $res = $service->run($ip);
        
        if(false === $res){
            $this->response->json(false, 1, $service->getLastError());
        }
        
        $this->response->json($res);
    }

}