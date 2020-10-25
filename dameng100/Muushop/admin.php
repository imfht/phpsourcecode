<?php
/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define ('APP_DEBUG', true );
/**
*定义应用目录
**/
define('BIND_MODULE','Admin');
define ('APP_PATH', './Application/' ); 
/**
*定义模板目录
**/
define ('MUUCMF_THEME_PATH', './Theme/');
/**
*定义缓存目录
**/
define ('RUNTIME_PATH', './Runtime/' );
/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
try{
    require './ThinkPHP/ThinkPHP.php';
}catch (\Exception $exception){
    if($exception->getCode()==815){
        send_http_status(404);
        $string=file_get_contents('./404.html');
        $string=str_replace('$ERROR_MESSAGE',$exception->getMessage(),$string);
        $string=str_replace('HTTP_HOST','http://'.$_SERVER['HTTP_HOST'],$string);
        echo $string;
    }else{
        E($exception->getMessage(),$exception->getCode());
    }
}