<?php

namespace Apphook;

use SCH60\Kernel\App;
use Common\AppCustomHelper;
use SCH60\Kernel\KernelHelper;

class InitControllerMicroServiceApi{
    
    public function run(){
        $this->disableSearchRobotInApi();
        
        $this->runAuthCommon();
        $this->runAuthSpecific();
        
        $hookCommon = KernelHelper::getInstance("Apphook\Common");
        $hookCommon->initAlibabaSDK();
        
    }
    
    
    protected function runAuthCommon(){
        
        //ip总限制
        $ips = KernelHelper::config("microserviceapi_allow_ip");
        
        if("*" !== $ips && !in_array($_SERVER['REMOTE_ADDR'], $ips)){
            App::$app->response->sendResponse(401);
            return App::$app->response->json(false, 1000, 'CONNECT_SERVER_IP_NOT_ALLOWED');
        }
        
    }
    
    
    protected function runAuthSpecific(){
        
        if(isset($_POST['r'])){
            App::$app->response->sendResponse(401);
            return App::$app->response->json(false, 1000, 'PARAM_R_MUST_BE_IN_URL');
        }
        
        $appver= isset($_GET['m_ver']) ? $_GET['m_ver'] : '';
        $ip = isset($_GET['m_ip']) ? $_GET['m_ip'] : '';
        if(empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
            App::$app->response->sendResponse(401);
            return App::$app->response->json(false, 1000, 'CONNECT_CLIENT_IP_NOT_RIGHT');
        }
        
        if(empty($appver) || $appver != KernelHelper::config('microserviceapi_version')){
            App::$app->response->sendResponse(401);
            return App::$app->response->json(false, 1000, 'AUTH_FAIL_APP_VER_WRONG');
        }
        
        $appid = isset($_GET['m_appid']) ? $_GET['m_appid'] : '';
        if(empty($appid)){
            App::$app->response->sendResponse(401);
            return App::$app->response->json(false, 1000, 'AUTH_FAIL_NO_APPID');
        }
        
        $appCfg = KernelHelper::config('client_app_'. $appid);
        if(empty($appCfg)){
            App::$app->response->sendResponse(401);
            return App::$app->response->json(false, 1000, 'AUTH_FAIL_APP_NOT_REGISTER');
        }
        
        if(!isset($appCfg['allow_ip']) || empty($appCfg['allow_ip'])){
            $appCfg['allow_ip'] = array('127.0.0.1', '0.0.0.0',);
        }
        
        if("*" !== $appCfg['allow_ip'] && !in_array($_SERVER['REMOTE_ADDR'], $appCfg['allow_ip'])){
            App::$app->response->sendResponse(401);
            return App::$app->response->json(false, 1000, 'CONNECT_SERVER_IP_NOT_ALLOWED_BY_APP_SETTING');
        }
        

        $validateClass = KernelHelper::config('microserviceapi_request_validate_class');
        if(empty($validateClass) || !class_exists($validateClass)){
            App::$app->response->sendResponse(401);
            return App::$app->response->json(false, 1000, 'API_SYSTEM_ERROR_REQUEST_VALIDATE_NOT_SET');
        }
        
        $requestValidate = new $validateClass();
        $requestValidate->run($appid, $appCfg);
        
    }
    
    protected function disableSearchRobotInApi(){
        if(App::$app->request->isRobot()){
            App::$app->response->sendResponse(403);
            return App::$app->response->json(false, 1000, 'EMPTY_USERAGENT_OR_ROBOT');
        }
    }
    
}