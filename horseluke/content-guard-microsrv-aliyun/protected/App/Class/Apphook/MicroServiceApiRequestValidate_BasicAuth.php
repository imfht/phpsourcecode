<?php

namespace Apphook;

use SCH60\Kernel\App;
use Common\AppCustomHelper;
use SCH60\Kernel\KernelHelper;

class MicroServiceApiRequestValidate_BasicAuth{
    
    public function run($appid, $appCfg){
        $ip = isset($_GET['m_ip']) ? $_GET['m_ip'] : '';
        
        $basicAuth = $this->findBasicAuthInHttpHeader();
        if(empty($basicAuth)){
            App::$app->response->sendResponse(401);
            return App::$app->response->json(false, 1000, 'AUTH_FAIL_NO_AUTH');
        }
        
        $pwdTime = $basicAuth['PHP_AUTH_USER'];
        $pwdDigest = $basicAuth['PHP_AUTH_PW'];
        
        if(!is_numeric($pwdTime) || abs($pwdTime - $_SERVER['REQUEST_TIME']) > KernelHelper::config('microserviceapi_auth_timediff_max')){
            App::$app->response->sendResponse(401);
            return App::$app->response->json(false, 1000, 'AUTH_FAIL_TIME_FAIL');
        }
        
        if(strtoupper(md5($appCfg['appsecret']. 'm_appid='. $appid. '&m_ip='. $ip.  '&m_time='. $pwdTime)) !== $pwdDigest){
            App::$app->response->sendResponse(401);
            return App::$app->response->json(false, 1000, 'AUTH_FAIL_VALID_FAIL');
        }
    }
    

    /**
     * @link http://php.net/manual/en/features.http-auth.php
     * @return array
     */
    protected function findBasicAuthInHttpHeader(){
        $return = array();
        if(isset($_SERVER['PHP_AUTH_USER'])){
            $return['PHP_AUTH_USER'] = $_SERVER['PHP_AUTH_USER'];
            $return['PHP_AUTH_PW'] = $_SERVER['PHP_AUTH_PW'];
            return $return;
        }
    
        if(!isset($_SERVER['HTTP_AUTHORIZATION'])){
            return $return;
        }
    
        $m = array();
        if(!preg_match('/^basic ([a-z0-9\+\/\=]+)$/i', $_SERVER['HTTP_AUTHORIZATION'], $m)){
            return $return;
        }
    
        $digest = base64_decode($m[1]);
        if(empty($digest)){
            return $return;
        }
    
        $split = stripos($digest, ":");
        if($split === false){
            $return['PHP_AUTH_USER'] = $digest;
            $return['PHP_AUTH_PW'] = "";
            return ;
        }
    
        $return['PHP_AUTH_USER'] = substr($digest, 0, $split);
        $return['PHP_AUTH_PW'] = substr($digest, $split + 1);
        if(false === $return['PHP_AUTH_PW']){
            $return['PHP_AUTH_PW'] = "";
        }
        return $return;
    
    }
    
    
}