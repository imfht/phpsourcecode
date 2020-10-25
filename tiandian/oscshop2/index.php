<?php
//判断php版本
if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');

header('X-Powered-By: oscshop2');
//设置网站字符集
header("Content-Type:text/html; charset=utf-8");
//版本号
define('OSCSHOP_VERSION', '2.0');
//根目录，物理路径
define('ROOT_PATH',str_replace('\\','/',dirname(__FILE__)) . '/'); 
//图片上传目录
define('DIR_IMAGE',ROOT_PATH.'public/uploads/');
//类库包
define('EXTEND_PATH','./extend/');
//扩展类库包
define('VENDOR_PATH','./vendor/');
// 定义应用目录
define('APP_PATH','./oscshop/');
//应用命名空间
define('APP_NAMESPACE','osc');

define('THEMES','default');

// 加载框架引导文件
require './thinkphp/start.php';
