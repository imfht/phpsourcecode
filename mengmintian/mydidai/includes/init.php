<?php
/**
 * 网站初始化文件
 */

//设置网站语言编码
header('Content-Type:text/html;charset=utf-8');

//定义网站的根目录
define('ROOT_PATH',  str_replace('\\','/',dirname(dirname(__FILE__)).'/'));


//加载自定义的函数
require  ROOT_PATH.'/includes/function.php';

//引入配置文件
require  ROOT_PATH.'/config/base.php';
require  ROOT_PATH.'/config/db.php';

/**
 * 自动加载类函数
 * @param  [type] $className [description]
 * @return [type]            [description]
 */
function auto_class($className){
    if(strtolower(substr($className, -5)) == 'model'){
        require ROOT_PATH.'model/'.$className.'.class.php';
    }else if(strtolower(substr($className, -4)) == 'tool'){
        require ROOT_PATH.'includes/class/'.$className.'.class.php';
    }else{
        require ROOT_PATH.'includes/'.$className.'.class.php';
    }
}

//注册自动加载类的函数
spl_autoload_register('auto_class');

IS_CACHE ? ob_start() : null;

DEBUG ? error_reporting(E_ALL & ~E_NOTICE) : error_reporting(0);
