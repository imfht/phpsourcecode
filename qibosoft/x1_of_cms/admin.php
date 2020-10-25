<?php
//define('SUPER_ADMIN',true); /*如忘记密码或权限丢失,请把开头的双斜杠“//”去掉,就能强制进后台.后台设置好权限后,记得再加上双斜杠*/
 

// [ 后台入口文件 ]

header('Content-Type:text/html;charset=utf-8');
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.5.0','<'))  die('PHP版本过低，最少需要PHP5.5，请升级PHP版本！');

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');

// 定义入口为admin
define('ENTRANCE', 'admin');

//验证码用到
define('ADMIN_FILE', ENTRANCE.'.php');



// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
