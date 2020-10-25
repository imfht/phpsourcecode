<?php
/**
 * 服务端
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/26
 * Time: 16:34
 */

if(PHP_SAPI !== 'cli'){
    die('only run in cli mode');
}
require __DIR__ . '/vendor/autoload.php';
define('TASK_ROOT_PATH',__DIR__);
define('APP_NAME','app');
$kernel = new Bjask\Kernel();
$kernel->run();
