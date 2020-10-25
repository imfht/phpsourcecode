<?php
//载入初始化文件
require_once __DIR__ . '/initServer.php';

Swoole\Config::$debug = false;

//设置PID文件的存储路径
Swoole\Network\Server::setPidFile(WEBPATH . '/server/pid/ftpServer.pid');

Swoole\Network\Server::start(function ($options)
{
    $ftpSvr = new Swoole\Protocol\FtpServer();
    $ftpSvr->users['test'] = [
        'password' => 'test',
        'home' => '/home/qianxun/website/swooleProject/',
        'chroot' => true,
    ];
    $ftpSvr->users['anonymous'] = [
        'password' => 'anon@localhost',
        'home' => '/tmp/',
        'chroot' => true,
    ];
    $server = Swoole\Network\Server::autoCreate('0.0.0.0', 21);
    $server->setProtocol($ftpSvr);
    $server->run(['worker_num' => 10]);
});
