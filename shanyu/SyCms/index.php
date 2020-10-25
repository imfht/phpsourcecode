<?php
// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('ERROR:PHP<5.3.0');

//判断是否安装
if(!file_exists('Common/Conf/db.php')){header('Location:Install/index.php');exit();}

define('APP_DEBUG',true);// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('BUILD_LITE_FILE',true);//生成Lite文件
define('BUILD_DIR_SECURE', false);//自动生成安全目录安全文件

define('WEB_PATH', str_replace("\\","/",getcwd()).'/');//定义网站根路径
define('APP_PATH', WEB_PATH.'Application/');//定义项目路径
define('RUNTIME_PATH', WEB_PATH . 'Runtime/');//定义缓存目录
define('HTML_PATH', RUNTIME_PATH. 'Html/');//定义应用静态缓存目录
define('COMMON_PATH', WEB_PATH.'Common/');// 应用公共目录
define('THINK_PATH', WEB_PATH. 'Frame/');//定义框架系统目录

if(!APP_DEBUG && is_file(RUNTIME_PATH.'lite.php')) require RUNTIME_PATH.'lite.php';//引入预编译文件
else require THINK_PATH.'ThinkPHP.php';//引入核心文件