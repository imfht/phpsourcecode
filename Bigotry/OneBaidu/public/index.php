<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

// 若点击详情则跳转百度官方
if(!empty($_GET['url'])) : $baidu_url = "http://www.baidu.com/link?url=".$_GET['url']; header('Location:'.$baidu_url); exit(); endif;

// 初始化系统
require  './init.php';

// 检测是否安装
file_exists(APP_PATH . 'database.php') ?  define('BIND_MODULE', 'index') : define('BIND_MODULE', 'install');

// 加载框架引导文件
require FRAMEWORK_START_PATH;
