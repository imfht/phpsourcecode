<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 系统入口
*/
error_reporting(E_ALL^E_NOTICE);

define("INPOP", true);
define('BASE_PATH', dirname(__FILE__)); //获取入口文件的路径
define('BASE_FILENAME', basename(__FILE__)); //获取入口文件的路径
define('DS', DIRECTORY_SEPARATOR); //调用系统目录的分隔,如win下为’\’或linux下为’/’
define('LIB_PATH', BASE_PATH.DS.'libs'); //定义基础库路径
define('EXT', '.php'); //定义后缀
define('VIEW_EXT', '.html'); //定义模板后缀
define('PLATFORM', 'default');//标准模式：default;新浪SAE模式：sae;阿里模式：ace;
define('ROUTER_TYPE', '/'); //定义路由分割

require(LIB_PATH.DS.'init'.EXT); //初始化
//启动
Frontend::Run();

?>