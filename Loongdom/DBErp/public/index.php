<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

use Zend\Mvc\Application;
use Zend\Stdlib\ArrayUtils;

chdir(dirname(__DIR__));

//如果在开发状态时，注释下面语句；在生产环境下，不要注释
//error_reporting(0);

include './vendor/autoload.php';

$appConfig = require './config/application.config.php';
if (file_exists('./config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require  './config/development.config.php');
}

Application::init($appConfig)->run();
