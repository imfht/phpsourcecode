<?php
/**
 * 网站程序的配置文件
 */

//分别定义网站模板目录，编译目录，缓存目录常量
define('TEMP_DIR',ROOT_PATH.'templates/');
define('COMP_DIR',ROOT_PATH.'compiles/');
define('CACHE_DIR',ROOT_PATH.'cache/');

//定义前后台的模板主题名称
define('INDEX_THEME','default');
define('ADMIN_THEME','default');

//定义是否开启调试模式
define('DEBUG',true);

//定义是否开启缓存功能
define('IS_CACHE',false);