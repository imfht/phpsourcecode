<?php
/**
 * 添加任务
 *
 * example:
 * php path/to/Example.php Hello
 *
 *
 * Hello指的是Template/Hello
 */
define('TASKFLOW_ROOT', __DIR__ . '/../');
require TASKFLOW_ROOT . 'vendor/autoload.php';
TaskFlow\Libraries\TaskFlow\Console::capsule();

$template = $_SERVER['argv'][1];

$task = TaskFlow\Libraries\TaskFlow\Model\Task::create([
    'name'   => $template,
    'status' => 'normal',
]);
