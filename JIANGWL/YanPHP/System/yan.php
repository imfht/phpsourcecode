<?php
/**
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/8/23
 * Time: 17:54
 */
defined('YAN_VERSION') or define('YAN_VERSION', '0.2');

//TODO 框架类库分离

$dirname = dirname(__FILE__);
require_once $dirname."/../vendor/autoload.php";

require_once $dirname."/Yan/Common/Functions.php";

use Yan\Core\Config;
use Yan\Core\Exception\FileNotExistException;
use Yan\Core\Exception\RuntimeException;
use Yan\Core\Log;
use Yan\Core\Session;
use Yan\Core\Database;
use Yan\Core\Dispatcher;
use Yan\Core\Validator;
use Yan\Core\Input;
use Yan\Core\ReturnCode;

Config::initialize();

Log::initialize();

set_exception_handler('exceptionHandler');
set_error_handler('errorHandler');

/**
 * Session
 */
Session::initialize();

/**
 * database
 */
Database::initialize();


/**
 * router
 */
Dispatcher::initialize();
$dispatch = \Yan\Core\Dispatcher::dispatch();

/**
 * Validator
 */
Validator::initialize();

/**
 * Input
 */
Input::initialize();

//根据Param/xxx.ini中配置的入参进行筛选
$paramFile = BASE_PATH . '/Param/' . Dispatcher::$controllerShortName . '.ini';
if (!file_exists($paramFile)) {
    throwErr("file {$paramFile} does not exist", ReturnCode::SYSTEM_ERROR, FileNotExistException::class);
}
$paramRules = parse_ini_file($paramFile, true);
if (!$paramRules) {
    throwErr("can not parse file {$paramFile}", ReturnCode::SYSTEM_ERROR, RuntimeException::class);
}
//规则验证
$input = array();
foreach ($paramRules[Dispatcher::$method] ?: [] as $key => $rule) {
    $value = Input::get($key) ?? null;
    $ret = Validator::validate($key, $value, $rule, $msg);
    if (!$ret) {
        throwErr($msg, ReturnCode::INVALID_ARGUMENT, InvalidArgumentException::class);
    }
    $input[$key] = $value;
}
//覆盖Input中data值，只设置配置文件中配置的key
Input::setData($input);

$controller = new $dispatch[0];
$result = call_user_func([$controller, $dispatch[1]]);

if (!$result instanceof \Yan\Core\Compo\ResultInterface) {
    throwErr('result is not instance of ResultInterface',ReturnCode::SYSTEM_ERROR,RuntimeException::class);
}

showResult($result);
