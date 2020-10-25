<?php

/**
 * basic config
 */
return [
        'swoole'        =>      [
                'worker_num' =>  8,    //开启两个worker进程
                //'task_worker_num'=>1,
                'max_request' => 3,   //每个worker进程max request设置为3次
                'dispatch_mode'=>3,
                //'task_max_request'=>0,
                'daemonize'     =>      false,
                //'log_file' => 'log/swoole.log',
                // 'log_level'     =>      1,
                //'heartbeat_check_interval'      =>      '60',  心跳检测
                //'heartbeat_idle_time'           =>      '600',  最大空闲时间
                'open_eof_check' => true, //打开EOF检测
                //'open_eof_split' => true, //打开EOF_SPLIT检测
                'package_eof' => PHP_EOL, //设置EOF
                //'open_cpu_affinity'     =>      true,  cpu亲和度
                //'cpu_affinity_ignore' => array(0, 1),  表示不使用0和1CPU
                // 'ssl_cert_file' => __DIR__.'/config/ssl.crt',  //设置SSL证书
                //'ssl_key_file' => __DIR__.'/config/ssl.key',
                // 'ssl_method' => SWOOLE_SSLv3_CLIENT_METHOD,
                //'ssl_ciphers' => 'ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv2:+EXP',
                'http_compression'  =>   true,  //only swoole 4.1+
                'user'  =>      'www',   //设置运行用户
                'group' =>      'www',
                'buffer_output_size' => 32 * 1024 *1024, //必须为数字  输出缓存
                'socket_buffer_size' => 128 * 1024 *1024, //必须为数字 内存缓存
        ],
        'server'        =>      [
                'host'          =>      '0.0.0.0',
                'port'          =>      9550,
                'mode'          =>      SWOOLE_PROCESS,
                'type'          =>      SWOOLE_SOCK_TCP
        ],

        'crawler'       =>      [
                'max_process'   =>      20,
                'rule'          =>      [
                        'processId'     =>      \swoole_table::TYPE_INT,
                        'timeId'        =>      \swoole_table::TYPE_INT,
                        'interval'      =>      \swoole_table::TYPE_INT,
                        'numberAccount' =>      \swoole_table::TYPE_INT
                ]
        ],
        'email' =>      [
                'account'       =>      '',
                'security'      =>      '',
        ]

];
