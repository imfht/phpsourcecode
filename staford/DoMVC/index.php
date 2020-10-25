<?php

/**
 * 框架入口文件
 * 路径定义原则：路径后面需加上目录分隔符（也就是常量DS）
 * @author 暮雨秋晨
 * @copyright 2014
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR', dirname(__file__) . DS);
define('FRAMEWORK', ROOT_DIR . 'Framework' . DS); //定义框架路径
define('APP', 'Application'); //定义应用名
define('APPLICATION', ROOT_DIR . APP . DS); //定义应用路径

require_once APPLICATION . 'Application.php'; //引入应用配置文件

require_once FRAMEWORK . 'Framework.php'; //引入框架入口文件

Router::init(Router::URL_PATHINFO); //使用pathinfo模式初始化路由

Dispatcher::dispatch(include (ROOT_DIR . 'config.php')); //使用框架配置初始化系统环境
