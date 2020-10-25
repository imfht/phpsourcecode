<?php
return [

    'port'           => 880,//端口
    'listen_ip'      => '127.0.0.1',//监听ip
    'php_map'        => 'php',//php文件类型映射,如是 html ，则 index.html 映射到index.php文件
    'pt_http_server_key' => 'pt_http_server',//在$_SERVER 下专用这个保存与应用程序的交互
    'max_memory'=>10,//最大内存
    //

    'package_max_length'=>8388608,

    'swoole'=>[
        'worker_process' => 2,//工作进程
        'task_worker_num'=>2,//task 任务进程数量
        'user' => 'nginx',
        'group' => 'nginx',
        'daemonize' => 1,//守护进程
        'log_file'=>'/var/log/swoole.log',//服务运行错误日志
    ],
    'web'=>[
        'movie.com'     => [
            //如果为空，上级http服务有指定 HTTP_DOCUMENT_ROOT,则取之
            //'document_root' => '/mnt/hgfs/www/xsh_movie/',
            'server_name'   => 'movie.com',//访问域名,多个域名用“,”分开

            'rewrite'       => TRUE,//重写功能，false 关闭 ,true 打开
            'index'         => 'index.php index.html index.htm',//目录默认访问文件名,多个用空格隔开
            //重写规则
            'rewrite_route'  => [//U是控制器参数，/app/control/action 三个层次
                                 '/^\/(.*?)\?([^\.].*?)$/iU' => '/index.php?U=$1',
                                 '/^\/(.*?)$/iU'             => '/index.php?U=$1',
            ],
            'data_timezone' => 'RPC',
            'is_cache'=>1,
            /**
             * 浏览器缓存
             * type 缓存的文件类型
             * time 缓存的时长（s）
             */
            'cache'=>[
                'type'=>['js','css','png','ico','jpg','gip','wff2'],'time'=>3
            ],
            'access_denied' => '',//拒绝访问的文件类型,空格隔开多个
            'exit_process'  =>1,//执行处理php后，是否退出进程,默认是退出
            //'redirect'=>['haodocs.com'=>'www.haodocs.com'],//301 跳转
            'session_expire'=>360000,//session 有效期
        ]
    ],
];