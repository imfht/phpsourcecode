<?php

/*************************************************************
* 框架入口文件
* @abstract 欢迎使用DouPHP框架，请按照您的意愿来扩展它吧
* @author 暮雨秋晨
* @copyright 2014
*************************************************************/

defined('ROOT_DIR') or die('[FatalError] Using the undefined constants ROOT_DIR');
define('FRAMEWORK_CORE', FRAMEWORK . 'Core' . DS); //定义框架核心类文件目录
define('FRAMEWORK_BASE', FRAMEWORK . 'Base' . DS); //定义框架基础类文件目录
define('FRAMEWORK_EXTS', FRAMEWORK . 'Exts' . DS); //定义框架扩展类文件目录
require_once FRAMEWORK . 'functions.php'; //载入预定义函数库文件
require_once FRAMEWORK . 'support.php'; //载入框架支持文件
