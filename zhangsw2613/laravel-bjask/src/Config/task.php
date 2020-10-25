<?php
/**
 * task相关配置
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/12/17
 * Time: 11:26
 */
return [
    'process_name' => 'php:laravel-bjask',
    'master_file_path' => storage_path('framework/pid'),
    'task_namespace' => 'app/Tasks',
    'max_coroutine' => 1024,//最大线程数
    'max_execute_time' => 10,//单个任务单次最大运行时间，防止一个任务被无限期挂起，单位秒
    'max_tries' => 10,//单任务最大等待次数,不要设太大
    'disallow_concurrent' => false,//禁止并发，当设置为true时单个任务需执行完之后才会接着执行
    'write_log' => true,//是否需要写日志
];