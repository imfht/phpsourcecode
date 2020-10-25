<?php

/*************************************************************
* 框架入口文件
* @abstract 欢迎使用DouPHP框架，请按照您的意愿来扩展它吧
* @author 暮雨秋晨
* @copyright 2014
*************************************************************/

/**
 * @abstract 判断是否定义了项目根目录
 */
defined('ROOT_DIR') or die('未定义项目根目录ROOT_DIR');

/**
 * @abstract 判断定义框架根目录，用户未定义时默认为项目根目录下DouPHP目录
 */
defined('DOU_ROOT_DIR') or define('DOU_ROOT_DIR', ROOT_DIR . DS . 'DoMVC');

/**
 * @abstract 判断是否定义了项目目录路径，未定义则启用默认目录
 * @param config 配置文件存放目录   =>CONFIG
 * @param include 类库文件存放目录    =>INC
 * @param controller 控制器存放目录    =>CONTROLLER
 * @param view 模板存放目录   =>VIEW
 */
defined('CONFIG') or define('CONFIG', ROOT_DIR . DS . 'config');
defined('INC') or define('INC', ROOT_DIR . DS . 'include');
defined('CONTROLLER') or define('CONTROLLER', ROOT_DIR . DS . 'controller');
defined('VIEW') or define('VIEW', ROOT_DIR . DS . 'view');

/**
 * @abstract 定义框架class目录
 */
define('DOU_CLASS_DIR', DOU_ROOT_DIR . DS . 'MVC');

/**
 * @abstract 判断、定义输出编码，默认UTF-8
 */
defined('OUTPUT_ENCODING') or define('OUTPUT_ENCODING', 'UTF-8');

/**
 * @abstract 判断定义错误输出控制，默认关闭
 */
defined('DEBUG') or define('DEBUG', 0);

/**
 * @abstract 判断、定义当前项目模板目录，默认为default目录
 */
defined('TEMPLATE') or define('TEMPLATE', 'default');

/**
 * @abstract 判断、定义SESSION是否启用，默认开启
 */
defined('SESSION') or define('SESSION', true);

/**
 * @abstract 引入框架支持文件
 */
require_once DOU_ROOT_DIR . DS . 'support.php';

/**
 * @abstract 开始项目引导
 */
$c = $_GET['c'] = (isset($_GET['c']) && !empty($_GET['c'])) ? $_GET['c'] :
    'index'; //判断控制器，不存在则定位到默认控制器
$a = $_GET['a'] = (isset($_GET['a']) && !empty($_GET['a'])) ? $_GET['a'] :
    'index'; //判断操作，不存在则定位到默认操作
$c = ucfirst(strtolower($c)) . 'Controller'; //先全部转小写，再首字母转大写
$a = strtolower($a); //操作名只能小写
if (is_file($c_class_file = CONTROLLER . DS . $c . '.class.php')) {
    require_once $c_class_file;
} else {
    die('控制器[' . $c . ']不存在');
}
if (!class_exists($c, false)) {
    die('未定义[' . $c . ']控制器');
}
$controller = new $c;
//如果控制器中存在init()方法，则优先运行此方法
if (method_exists($controller, 'init')) {
    $controller->init();
}
//判断、执行操作
if (!method_exists($controller, $a)) {
    die('当前操作[' . $a . ']不存在');
}
$controller->$a();
