<?php
/**
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/8/23
 * Time: 17:06
 */

$debug = false;

if ($debug) {
    ini_set('display_errors', 1);            //错误信息
    ini_set('display_startup_errors', 1);    //php启动错误信息
    error_reporting(-1);                    //打印出所有的 错误信息
}


$systemPath = '../../System';

$cachePath = 'cache';

defined('SELF') or define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

defined('SYS_PATH') or define('SYS_PATH', rtrim($systemPath, '/\\'));

defined('BASE_PATH') or define('BASE_PATH', dirname(__FILE__));

defined('CACHE_PATH') or define('CACHE_PATH', $cachePath);


require_once "../../System/yan.php";