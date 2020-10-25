<?php

// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('ERROR:PHP<5.3.0');

define('MODE_NAME','api');
define('BIND_MODULE','Api');

define('APP_DEBUG',false);// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false

define('WEB_PATH', str_replace("\\","/",getcwd()).'/');//定义网站根路径
define('APP_PATH', WEB_PATH.'Application/');//定义项目路径
define('RUNTIME_PATH', WEB_PATH . 'Runtime/');//定义缓存目录
define('HTML_PATH', RUNTIME_PATH. 'Html/');//定义应用静态缓存目录
define('COMMON_PATH', WEB_PATH.'Common/');// 应用公共目录
define('THINK_PATH', WEB_PATH. 'Frame/');//定义框架系统目录



require THINK_PATH.'ThinkPHP.php';//引入核心文件