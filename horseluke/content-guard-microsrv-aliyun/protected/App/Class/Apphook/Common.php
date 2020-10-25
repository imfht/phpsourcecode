<?php

namespace Apphook;

use SCH60\Kernel\App;
use SCH60\Kernel\KernelHelper;

class Common{
    
    function check_post_referer(){
        if(strtoupper($_SERVER['REQUEST_METHOD']) != 'POST'){
            return ;
        }
    
        $str = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $domainList = KernelHelper::config('site_domain_allow_referer');
    
        if(empty($str)){
            App::$app->response->error("来源为空");
        }
    
        preg_match('/^(?:http|https):\/\/([a-z0-9-_.]+)/', $str, $m);
        if(!isset($m[1])){
            App::$app->response->error("来源不合规");
        }
    
        $str = $m[1];
    
        if(empty($domainList)){
            $domainList = array();
        }
        $domainList[] = $_SERVER['HTTP_HOST'];
    
        foreach($domainList as $domain){
            if($domain === $str){
                return true;
            }
    
            $domain_regex = '/[a-z0-9.]*'. ($domain{0} !== '.' ? '\.' : ''). str_replace('.', '\.', $domain). '(:[0-9]+){0,1}$/i';
            if(preg_match($domain_regex, '.'. $str)){
                return true;
            }
        }
    
        App::$app->response->error("来源不正确");
    
    }
    
    /**
     * 后台必须禁止搜索引擎抓取
     */
    function disableSearchRobot(){
        if(App::$app->request->isRobot()){
            App::$app->response->error("禁止访问", 403);
        }
    }
    
    function initAlibabaSDK(){
        $cfg = array(
            'configFile' => D_APP_DIR. '/Config/'. KernelHelper::config('ALIBABASDK_SERVICELOCATOR_CONFIG_FILENAME'). '.php',
        );
        \AlibabaSDK\Integrate\ServiceLocator::setInstanceDefaultConfig($cfg);
    }
    
    
}