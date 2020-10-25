<?php

namespace workerbase\cli;

use workerbase\classs\Config;
use workerbase\classs\App;
use workerbase\classs\Error;
/**
 * 定时任务执行入口
 * @author fukaiyao
 */
// fix for fcgi
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

//定义app id
define('WK_APP_ID', "cron");
define('WORKER_PROJECT_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

require_once WORKER_PROJECT_PATH . 'workerbase/helper.php';
require_once WORKER_PROJECT_PATH . 'workerbase/vendor/autoload.php';

date_default_timezone_set('PRC');
loadc('Loader')->run();

//初始化当前系统环境
define('WK_ENV',  Config::read('env'));

//定义cron环境
define('IS_WK_CRON', true);

// 注册错误和异常处理机制
Error::register();
App::run();

//启动定时任务服务
\workerbase\classs\Crond::getInstance()->start();
