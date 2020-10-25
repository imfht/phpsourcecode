<?php
return array(
    'server' => array(
        'worker_num' => 4, //工作进程数量
        'task_worker_num' => 4, //任务工作线程数量
        'daemonize' => false, //是否作为守护进程
        'heartbeat_idle_time' => 360, //心跳超时时间(6分钟)
        'open_cpu_affinity' => 1, //启用CPU亲和设置
        'open_tcp_nodelay' => 1, //启用tcp_nodelay
        'package_length_type' => 'N', //数据表长度类型
        'package_length_offset' => 0, //包长度偏移位
        'package_max_length' => 2097152, //包最大长度(2M)
        'max_request' => 1000, //worker进程最大请求数
        'backlog' => 128, //accept queue
        'log_file' => 'swoole.log', //log file
        'max_conn' => 8000,
    ),
    'log_level' => 0,
    'log_mode' => ['file', 'console'],
    'log_path' => 'log/szserver.log',
);