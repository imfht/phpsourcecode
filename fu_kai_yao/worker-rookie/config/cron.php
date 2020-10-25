<?php
/**
 * 定时任务
 */
return [
    'cmd_path' => [
        //根目录下对应的命令文件夹
        'path'  => 'apps/console/',
        //对应的命名空间
        'namespace'  => 'apps\console',
        //后缀
        'suffix' => 'Command',
    ],
    "pid" => WORKER_PROJECT_PATH .'/runtime/log/workerlog/crond.pid',
    "log" => WORKER_PROJECT_PATH .'/runtime/log/workerlog/crond.log',
    //本地任务命令路径
    "cmd" => WORKER_PROJECT_PATH .'/scripts/cmd',
    //进程运行角色
    "user"   => 'root',
    //定时任务执行模式 c协程模式，p进程模式
    "schema"   => 'p',
    //远端任务配置
    "cronConf"   => [
//        'jobName' => [
//              //服务器地址
//              "host" => 'http://fankongyuan.com',
//              //端口
//              "port" => '8090',
//              //路径
//              "path" => '/api/test/test',
//              //rsa公钥
//              "publicKey" => '',
//              //rsa私钥
//              "privateKey" => '',
//        ],
    ],
    //定时任务
    'jobs' => [
//        [
//            'id' => 'test_job1',
//            "jobName" => "defaultJob",
//            'title' => '测试任务',
//             //定时配置，相对于linux的crontab, 系统支持精确到秒，第一位就是秒的配置，格式跟系统的crontab配置一样
//            'cron' => '* * * * * *',
//            'command' => 'test test',
//            //任务类型：L本地任务，R远程任务（远程任务必须配置jobName和对应的cronConf项）
//            "runType" => 'R',
//        ],
        [
            'id' => 'redis_queue_compensator',
            'title' => '每1分钟检查redis消息队列里是否有执行失败的任务',
            'cron' => '0 */1 * * * *',
            'command' => 'MqCompensator queueCompensator',
        ],
        [
            'id' => 'cron_test_test',
            'title' => '测试定时任务',
            'cron' => '*/1 * * * * *',
            'command' => 'test test',
        ],

    ]
];