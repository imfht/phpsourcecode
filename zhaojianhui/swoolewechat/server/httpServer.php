<?php
//载入初始化文件
require_once __DIR__ . '/initServer.php';

Swoole\Config::$debug = false;
//设置PID文件的存储路径
Swoole\Network\Server::setPidFile(WEBPATH . '/server/pid/httpServer.pid');
/**
 * 显示Usage界面
 * php app_server.php start|stop|reload
 */
Swoole\Network\Server::start(function () {
    $AppSvr = new Swoole\Protocol\HttpServer();
    $AppSvr->loadSetting(WEBPATH . '/server/swoole.ini'); //加载配置文件
    $AppSvr->setDocumentRoot(WEBPATH . '/public/');
    $AppSvr->setLogger(new Swoole\Log\EchoLog(true)); //Logger

    Swoole\Error::$echo_html = false;

    $server = Swoole\Network\Server::autoCreate('0.0.0.0', 8888);
    $server->setProtocol($AppSvr);
    //$server->daemonize(); //作为守护进程
    $server->run(['worker_num' => 0, 'max_request' => 5000, 'log_file' => WEBPATH.'/logs/http-swoole.logs']);
});
