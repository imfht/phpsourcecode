<?php
/**
 * 项目配置文件
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/26
 * Time: 11:24
 */

return [
    //进程相关配置
    'processName' => 'php: bjask-process',//进程名前缀加php关键字方便管理
    'isDaemon' => 1,//是否以守护进程运行
    'pidFilePath' => TASK_ROOT_PATH . '/logs/pids/',//进程pid保存文件目录
    'maxChildProcess' => 10,//最大子进程数
    'maxExecuteTime' => 10,//子进程最大执行时间
    'openMessage' => 1,//开启消息提醒
    'runSleep' =>  100,//任务执行完暂停毫秒数
    'maxQueue' => 100,//积压队列长度超过该值触发报警
    'queueTickTime' => 1000 * 100,//定时检查队列长度
    'messageType' => 'ding',//报警消息类型:ding,sms
    //任务相关配置
    'topic' => [
        'mytask1' => ['minProcess' => 1, 'maxProcess' => 3],//每个任务可开启的进程数区间
        'mytask2' => ['minProcess' => 1, 'maxProcess' => 3]
    ],
    //日志相关配置
    'log' => [
        'prefix' => 'bjask',
        'log_path' => 'logs',
        'max_file_size' => 100,
        'max_buffer' => 10000,
    ],
    //队列相关配置
    'queue' => [
        /*'driver' => 'rabbitmq',
        'host' => '127.0.0.1',
        'port' => '5672',
        'user' => 'guest',
        'pass' => 'guest',
        'vhost' => '/'*/
        'driver' => 'redis',
        'host' => '127.0.0.1',
        'port' => '6381',
        'password'=> ''
    ],
    //消息相关配置
    'message' => [
        'ding' => [
            'url' => 'https://oapi.dingtalk.com/robot/send?access_token=',
        ],
        'sms' => []
    ],
    'ext_files' => 'app.php',
];