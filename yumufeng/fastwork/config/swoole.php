<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 12:53
 */

use fastwork\facades\Env;

return [
    //server设置
    'ip' => '0.0.0.0',   //监听IP
    'port' => 8098,        //监听端口
    'server' => 'websocket',     //服务，可选 websocket 默认http
    'set' => [            //配置参数  请查看  https://wiki.swoole.com/wiki/page/274.html
        'daemonize' => APP_DEBUG ? false : true,
        'enable_static_handler' => true,
        'document_root' => Env::get('root_path') . 'public',
        'worker_num' => 2,
        'max_request' => 10000, // 一个worker进程在处理完超过此数值的任务后将自动退出
        'task_worker_num' => 2,
        'task_max_request' => 100, // 一个task进程在处理完超过此数值的任务后将自动退出
        'task_tmpdir' => Env::get('runtime_path') . 'task',
        'task_enable_coroutine' => true, //v4.2.12起支持
        'reload_async' => true, // 柔性异步重启，会等待所有协程退出后重启
        //swoole的pid和日志配置
        'pid_file' => Env::get('runtime_path') . 'swoole.pid',
        'log_file' => APP_DEBUG ? null : Env::get('runtime_path') . 'swoole.log',
        //websocket心跳配置
        'heartbeat_check_interval' => 10, // 此选项表示每隔多久轮循一次
        'heartbeat_idle_time' => 60,
    ],
    'monitor' => [
        'timer' => 1500,  //定时器间隔时间，单位毫秒
        'debug' => APP_DEBUG,       //重启
        'path' => [
            Env::get('app_path'),
            Env::get('route_path')
        ]
    ],

];
