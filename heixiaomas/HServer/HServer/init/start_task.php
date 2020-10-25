<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/9/12
 * Time: 18:00
 */

require_once __DIR__ . "/../TimeWorker.php";

require_once __DIR__ . '/../load.php';


use Workerman\Worker;
use HServer\TimeWorker;

$task = new TimeWorker();
$task->name = "定时器业务";

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}