<?php

/* 
 * miniLog 日志实例操作
 * @author 300js
 * @date 2017/01/11
 * 
  可以在外部更改的常量:
   支持html便捷浏览模式或纯txt查看,值html|txt
   defined('MINI_DEBUG_TYPE') or define('MINI_DEBUG_TYPE', 'html');
   调试模式,1可写,0不可写
   defined('MINI_DEBUG_FLAG') or define('MINI_DEBUG_FLAG', 1);
   jquery 地址
   defined('MINI_DEBUG_JSPAHT') or define('MINI_DEBUG_JSPAHT', 'http://cdn.bootcss.com/jquery/1.8.3/jquery.js');
   debug 可写的目录设置,结尾一定要加 保证有可写权限
   defined('MINI_DEBUG_PATH') or define('MINI_DEBUG_PATH', __DIR__ . DIRECTORY_SEPARATOR);
 */
require '../miniLog/miniLog.php';
$action = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
define('MINI_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');
if($action == 'change_dir') {
    //更改存储目录:
    define('MINI_DEBUG_PATH', MINI_PATH .'logs/');//定义日志存储目录,必须后面加斜杆 
    miniLog::log('更改存储目录', '更改存储目录');    
    $filename = str_replace(MINI_PATH, '', '/'.miniLog::getCacheFileName());    
    exit(setAjaxMsg(0, $filename));
}
elseif($action == 'change_file_name') {
    //存储不同的文件名:
    define('MINI_DEBUG_PATH', MINI_PATH .'logs/');
    miniLog::setCacheFile(date('Y-m-d'));//定义生成不同的日志文件格式,无需设置文件后缀
    miniLog::log('存储不同的文件名', '存储不同的文件名');
    $filename = str_replace(MINI_PATH, '', '/'.miniLog::getCacheFileName());    
    exit(setAjaxMsg(0, $filename));
}
elseif($action == 'change_save_format') {
     //  更改存储格式:
    define('MINI_DEBUG_PATH', MINI_PATH .'logs/');
    define('MINI_DEBUG_TYPE', 'txt');//定义日志记录格式,默认为html
    miniLog::log('更改存储格式', '更改存储格式');
    $filename = str_replace(MINI_PATH, '', '/'.miniLog::getCacheFileName());    
    exit(setAjaxMsg(0, $filename));
}
elseif($action == 'flush_data') {
    // 覆盖文件,相当将之前的数据删除,写入新的数据,可做清空数据用
    define('MINI_DEBUG_PATH', MINI_PATH .'logs/');
    miniLog::log(1, '清空数据', false);//清空数据
    $filename = str_replace(MINI_PATH, '', '/'.miniLog::getCacheFileName());    
    exit(setAjaxMsg(0, $filename));
}
elseif($action == 'pack_ok') {
    //封装好,全局快速调用
    mylog('封装好,全局快速调用', '封装好,全局快速调用');
    $filename = str_replace(MINI_PATH, '', '/'.miniLog::getCacheFileName());    
    exit(setAjaxMsg(0, $filename));
}

/**
 * 设置返回的json数据格式
 * @param type $status
 * @param type $msg
 * @return type
 */
function setAjaxMsg($status, $msg = ''){
    return json_encode(array('status' => $status, 'msg' => $msg));
}

/**
 * 封装好,全局快速调用
 * @param type $data
 * @param type $memo
 * @param type $isFlush
 */
function mylog($data, $memo, $isFlush = false) {
    define('MINI_DEBUG_PATH', MINI_PATH .'logs/');
    miniLog::log($data, $memo, $isFlush);//清空数据
}
 

 