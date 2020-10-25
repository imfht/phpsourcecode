<?php
/**
 * @package     Demo.php
 * @author      Jing Tang <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net/
 * @version     2.0
 * @copyright   Copyright (c) http://www.slimphp.net
 * @date        2017年6月7日
 */

$application = require __DIR__ . '/../../SlimCustom/index.php';
$application->setName('Demo')->setPath(realpath(__DIR__ . '/../'))->boot();

$daemon = Daemon();

$daemon->group('Demo', function () {
    // 注册任务‘task1’，每隔10秒运行
    $this->call(10, 'task1', function () {
        while (true) {
            logger()->info(getmypid());
            sleep(1);
        }
        //sleep(10);
        exit;
    });
    // 注册任务‘task2’，每隔5秒运行
    $this->call(5, 'task2', function () {
        while (true) {
            logger()->info(getmypid());
            sleep(1);
        }
        //sleep(5);
        exit;
    });
});

// 启动任务调度守护进程
$daemon->run();