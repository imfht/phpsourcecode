<?php
namespace workerbase\cli;

use workerbase\classs\Config;
use workerbase\classs\App;
use workerbase\classs\Error;

/**
 * worker工作进程执行入口
 * @author fukaiyao
 */

// fix for fcgi
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

//定义app id
define('WK_APP_ID', "worker");
//定义项目根目录
define('WORKER_PROJECT_PATH',  __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

require_once WORKER_PROJECT_PATH.'workerbase/helper.php';
require_once WORKER_PROJECT_PATH.'workerbase/vendor/autoload.php';

date_default_timezone_set('PRC');
loadc('Loader')->run();

//初始化当前系统环境
define('WK_ENV',  Config::read('env'));

//定义worker环境
define('IS_WK_WORKER', true);

// 注册错误和异常处理机制
Error::register();
App::run();

$options = getopt('t:');
if (!isset($options['t']) || empty($options['t'])) {
    echo "invalid params.";
    exit();
}

$jobName = $options['t'];
\workerbase\classs\worker\Worker::getInstance($jobName)->run();
