<?php
/**
 * 执行任务
 *
 * example:
 * php path/to/Run.php
 * 挂起消费
 *
 * example:
 * php path/to/Run.php 1
 * 执行单个任务的最新子任务, 1指的是task表中的主键
 */

if (!defined('TASKFLOW_ROOT')) {
    define('TASKFLOW_ROOT', __DIR__ . '/../');
}

require TASKFLOW_ROOT . 'vendor/autoload.php';

if (isset($_SERVER['argv'][1])) {
    $taskId = $_SERVER['argv'][1];
    TaskFlow\Libraries\TaskFlow\Console::run($taskId);
    return true;
}

while (true) {
    sleep(1);
    TaskFlow\Libraries\TaskFlow\Console::run();
}
