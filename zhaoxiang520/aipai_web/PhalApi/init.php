<?php
/**
 * init.php
 * @since   2016-08-26
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi;

ini_set('memory_limit', '-1');
//定义部分系统常量
define('DS', DIRECTORY_SEPARATOR);
define('PHALAPI_START_TIME', microtime(true));
define('PHALAPI_START_MEM', memory_get_usage());
defined('APP_VERSION') || define('APP_VERSION', 'V1.2');
//注册自动加载类库
require_once DOCUMENT_ROOT.'/PhalApi/Core/AutoLoad.php';
require_once DOCUMENT_ROOT.'/PhalApi/Common/functions.php';
Core\AutoLoad::register();

use PhalApi\Core\App;
use PhalApi\Core\Exception\PAException;
use PhalApi\Core\HTTP;
use PhalApi\Core\URL;

//验证PHP版本
if (version_compare(PHP_VERSION, '5.6.0') < 0) {
    throw new PAException("当前PHP版本太低,请使用5.6以上版本的PHP!");
}
URL::init();
HTTP::init();
App::run();

