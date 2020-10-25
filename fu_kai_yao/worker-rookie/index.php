<?php
use workerbase\classs\Config;
use workerbase\classs\App;
use workerbase\classs\Error;

//定义app id
define('WK_APP_ID', "api");
//定义项目根目录
define('WORKER_PROJECT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once WORKER_PROJECT_PATH.'workerbase/helper.php';
require_once WORKER_PROJECT_PATH.'workerbase/vendor/autoload.php';

date_default_timezone_set('PRC');
loadc('Loader')->run();

//初始化当前系统环境
define('WK_ENV',  Config::read('env'));

// 注册错误和异常处理机制
Error::register();
App::run();
require WORKER_PROJECT_PATH.'router/router.php';
App::end();